<?php

namespace App\Http\Controllers;

use App\Models\TrackingSystem;
use App\Models\TravelDocument;
use App\Models\TravelDocumentAttachment;
use App\Models\Items;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Exports\ShippingsExport;
use App\Models\DeliveryConfirmation;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AdminWebController extends Controller
{
    // ========================================
    // SHIPPING MANAGEMENT
    // ========================================

    /**
     * Display list of travel documents
     */
    public function shippingsIndex(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $dateType = $request->query('date_type', 'document'); // document | posting
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = TravelDocument::query();

        // SEARCH (travel_document + items)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('no_travel_document', 'like', "%{$search}%")
                    ->orWhere('send_to', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('project', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where(function ($i) use ($search) {
                            $i->where('item_name', 'like', "%{$search}%")
                                ->orWhere('item_code', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                        });
                    });
            });
        }

        // STATUS FILTER
        if ($status !== '') {
            $query->where('status', $status);
        }

        // DATE FILTER
        $dateColumn = $dateType === 'posting' ? 'posting_date' : 'document_date';

        if (!empty($startDate)) {
            $query->whereDate($dateColumn, '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate($dateColumn, '<=', $endDate);
        }

        // Pagination (wajib appends supaya query string kebawa)
        $listTravelDocument = $query
            ->with(['items.unit']) // optional: kalau detail butuh unit
            ->latest('id')
            ->paginate(10)
            ->appends($request->query());

        // Stats Cards (opsional):
        // Kalau kamu ingin stats mengikuti filter saat ini, pakai clone query yang sama.
        // Kalau ingin tetap stats global, pakai TravelDocument::selectRaw seperti sebelumnya.
        $statsQuery = TravelDocument::query();

        // Terapkan filter yang sama untuk stats agar konsisten dengan hasil tabel
        if ($search !== '') {
            $statsQuery->where(function ($q) use ($search) {
                $q->where('no_travel_document', 'like', "%{$search}%")
                    ->orWhere('send_to', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('project', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where(function ($i) use ($search) {
                            $i->where('item_name', 'like', "%{$search}%")
                                ->orWhere('item_code', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                        });
                    });
            });
        }
        if ($status !== '') {
            $statsQuery->where('status', $status);
        }
        if (!empty($startDate)) {
            $statsQuery->whereDate($dateColumn, '>=', $startDate);
        }
        if (!empty($endDate)) {
            $statsQuery->whereDate($dateColumn, '<=', $endDate);
        }

        $stats = $statsQuery
            ->selectRaw(
                "
            SUM(CASE WHEN status = 'Belum terkirim' THEN 1 ELSE 0 END) as belum_terkirim,
            SUM(CASE WHEN status = 'Sedang dikirim' THEN 1 ELSE 0 END) as sedang_dikirim,
            SUM(CASE WHEN status = 'Terkirim' THEN 1 ELSE 0 END) as terkirim,
            COUNT(*) as total
        ",
            )
            ->first();

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Manajemen Pengiriman', 'url' => '#']];

        return view('General.shippings', [
            'listTravelDocument' => $listTravelDocument,
            'totalPengiriman' => $stats->total,
            'totalBelumTerkirim' => $stats->belum_terkirim,
            'totalSedangDikirim' => $stats->sedang_dikirim,
            'totalTerkirim' => $stats->terkirim,
            'breadcrumbs' => $breadcrumbs,
            // biar blade gampang autofill
            'filters' => [
                'search' => $search,
                'status' => $status,
                'date_type' => $dateType,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Search travel documents by multiple criteria
     */
    public function searchDocument(Request $request)
    {
        $query = $request->input('search');

        $results = TravelDocument::query()
            ->with(['items.unit'])
            ->where(function ($q) use ($query) {
                $q->where('no_travel_document', 'like', "%{$query}%")
                    ->orWhere('send_to', 'like', "%{$query}%")
                    ->orWhere('status', 'like', "%{$query}%")
                    ->orWhere('project', 'like', "%{$query}%")
                    ->orWhereHas('items', function ($itemQuery) use ($query) {
                        $itemQuery
                            ->where('item_name', 'like', "%{$query}%")
                            ->orWhere('item_code', 'like', "%{$query}%")
                            ->orWhere('description', 'like', "%{$query}%");
                    });
            })
            ->latest('id')
            ->get();

        return response()->json(['results' => $results]);
    }

    /**
     * Show detail of specific travel document
     */
    public function shippingsDetail($id)
    {
        // Tambahkan relasi attachments
        $travelDocument = TravelDocument::with(['items.unit', 'items.subItems.unit', 'attachments'])->findOrFail($id);

        $subCount = $travelDocument->items->sum(fn($item) => $item->subItems ? $item->subItems->count() : 0);

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')], ['label' => 'Detail Pengiriman', 'url' => '#']];

        return view('General.shippings-detail', compact('travelDocument', 'breadcrumbs', 'subCount'));
    }

    /**
     * Show form to add new travel document
     */
    /**
     * Show form to add new travel document
     */
    public function shippingsAdd()
    {
        $units = Unit::all();

        $items = [];

        // Jika validation gagal, rebuild dari old()
        if (old('itemName')) {
            $count = count(old('itemName'));

            for ($i = 0; $i < $count; $i++) {
                $items[] = [
                    'itemCode' => old("itemCode.$i"),
                    'itemName' => old("itemName.$i"),
                    'no' => old("no.$i"),
                    'quantitySend' => old("quantitySend.$i"),
                    'unitType' => old("unitType.$i"),
                    'description' => old("description.$i"),
                    'totalSend' => old("totalSend.$i"),
                    'information' => old("information.$i"),
                    'qtyPreOrder' => old("qtyPreOrder.$i"),

                    // 🔥 GROUP TITLE
                    'subItemGroupTitle' => old("subItemGroupTitle.$i"),

                    // Sub item akan di-handle blade via old("subItems.$i")
                    'subItems' => [],
                ];
            }
        } else {
            // Default pertama kali buka halaman
            $items[] = [
                'itemCode' => '',
                'itemName' => '',
                'no' => '',
                'quantitySend' => '',
                'unitType' => '',
                'description' => '',
                'totalSend' => '',
                'information' => '',
                'qtyPreOrder' => '',
                'subItemGroupTitle' => '',
                'subItems' => [],
            ];
        }

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')], ['label' => 'Tambah Pengiriman', 'url' => '#']];

        return view('General.shippings-add', compact('units', 'items', 'breadcrumbs'));
    }

    /**
     * Show form to edit travel document
     */

    public function shippingsEdit($id)
    {
        $travelDocument = TravelDocument::with(['items.unit', 'items.subItems.unit', 'attachments'])->findOrFail($id);

        $units = Unit::all();

        // =====================
        // BUILD ITEMS
        // =====================
        $items = [];

        // ======================================
        // CASE 1: VALIDATION ERROR (old input)
        // ======================================
        if (old('itemName')) {
            $count = count(old('itemName'));

            for ($i = 0; $i < $count; $i++) {
                $items[] = [
                    'itemName' => old("itemName.$i"),
                    'no' => old("no.$i"),
                    'itemCode' => old("itemCode.$i"),
                    'quantitySend' => old("quantitySend.$i"),
                    'unitType' => old("unitType.$i"),
                    'description' => old("description.$i"),
                    'totalSend' => old("totalSend.$i"),
                    'information' => old("information.$i"),
                    'qtyPreOrder' => old("qtyPreOrder.$i"),

                    // 🔥 GROUP TITLE
                    'subItemGroupTitle' => old("subItemGroupTitle.$i"),

                    // SubItems akan dibaca langsung via old("subItems.$i") di blade
                    'subItems' => [],
                ];
            }
        } else {
            // ======================================
            // CASE 2: LOAD DARI DATABASE
            // ======================================
            foreach ($travelDocument->items as $item) {
                $subItems = [];

                if ($item->subItems && $item->subItems->isNotEmpty()) {
                    foreach ($item->subItems as $sub) {
                        $subItems[] = [
                            'item_name' => $sub->item_name,
                            'item_code' => $sub->item_code,
                            'unit_id' => $sub->unit_id,
                            'qty_send' => $sub->qty_send,
                            'qty_po' => $sub->qty_po,
                            'total_send' => $sub->total_send,
                            'information' => $sub->information,
                            'description' => $sub->description ?? '',
                        ];
                    }
                }

                $items[] = [
                    'itemName' => $item->item_name,
                    'no' => $item->no,
                    'itemCode' => $item->item_code,
                    'quantitySend' => $item->qty_send,
                    'unitType' => $item->unit_id,
                    'description' => $item->description,
                    'totalSend' => $item->total_send,
                    'information' => $item->information,
                    'qtyPreOrder' => $item->qty_po,

                    // 🔥 GROUP TITLE DARI DB
                    'subItemGroupTitle' => $item->sub_item_group_title,

                    'subItems' => $subItems,
                ];
            }
        }

        // =====================
        // BUILD ATTACHMENTS
        // =====================
        $attachments = [];

        if (old('attachments')) {
            foreach (old('attachments') as $name) {
                $attachments[] = ['name' => $name];
            }
        } else {
            foreach ($travelDocument->attachments as $att) {
                $attachments[] = ['name' => $att->name];
            }
        }

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')], ['label' => 'Edit Pengiriman', 'url' => '#']];

        return view('General.shippings-edit', compact('travelDocument', 'units', 'items', 'attachments', 'breadcrumbs'));
    }

    /**
     * Store new travel document with items
     */
    public function shippingsAddTravelDocument(Request $request)
    {
        $validator = $this->validateTravelDocument($request, true);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $travelDocument = $this->createTravelDocument($validated);

            $this->createTravelDocumentItems($travelDocument, $validated);

            $this->syncAttachments($travelDocument, $validated['attachments'] ?? [], true);

            DB::commit();
            return redirect()->route('shippings.index')->with('success', 'Data pengiriman berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleShippingError($e, 'Terjadi kesalahan saat menyimpan data pengiriman.', ['action' => 'create', 'numberSJN' => $request->input('numberSJN')])->withInput();
        }
    }

    /**
     * Update existing travel document
     */
    public function shippingsUpdate(Request $request, $id)
    {
        $validated = $request->validate($this->getValidationRules(), $this->getValidationMessages());

        DB::beginTransaction();
        try {
            $travelDocument = TravelDocument::findOrFail($id);

            $this->updateTravelDocument($travelDocument, $validated);

            $travelDocument->items()->delete();

            $this->createTravelDocumentItems($travelDocument, $validated);

            $this->syncAttachments($travelDocument, $validated['attachments'] ?? [], true);

            DB::commit();
            return redirect()->route('shippings.index')->with('success', 'Data pengiriman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleShippingError($e, 'Terjadi kesalahan saat memperbarui data pengiriman.', ['action' => 'update', 'travel_document_id' => $id, 'numberSJN' => $request->input('numberSJN')])->withInput();
        }
    }

    /**
     * Save As - Create new travel document from existing one
     */
    public function shippingsSaveAs(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            array_merge($this->getValidationRules(), [
                'numberSJN' => 'required|string|max:100|unique:travel_document,no_travel_document',
            ]),
            array_merge($this->getValidationMessages(), [
                'numberSJN.unique' => 'Nomor SJN sudah digunakan. Gunakan nomor yang berbeda.',
            ]),
            $this->getValidationAttributes($request),
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $exists = TravelDocument::where('no_travel_document', $validated['numberSJN'])->exists();
            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Nomor SJN sudah digunakan. Gunakan nomor yang berbeda.');
            }

            $newTravelDocument = $this->createTravelDocument($validated);

            $this->createTravelDocumentItems($newTravelDocument, $validated);

            $this->syncAttachments($newTravelDocument, $validated['attachments'] ?? [], true);

            DB::commit();
            return redirect()
                ->route('shippings.index')
                ->with('success', 'Data pengiriman berhasil disimpan sebagai dokumen baru dengan nomor: ' . $validated['numberSJN']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleShippingError($e, 'Terjadi kesalahan saat menyimpan data pengiriman.', ['action' => 'create', 'numberSJN' => $request->input('numberSJN')])->withInput();
        }
    }

    /**
     * Delete travel document and its items
     */
    public function shippingsDelete($id)
    {
        DB::beginTransaction();
        try {
            $travelDocument = TravelDocument::findOrFail($id);
            // Soft delete akan otomatis handle items karena cascade
            $travelDocument->delete();

            DB::commit();
            return redirect()->route('shippings.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleShippingError($e, 'Terjadi kesalahan saat menghapus data pengiriman.', ['action' => 'delete', 'travel_document_id' => $id]);
        }
    }

    // ========================================
    // PRINT & EXPORT
    // ========================================

    /**
     * Generate PDF for travel document
     */
    public function printShippings($id)
    {
        $travelDocument = TravelDocument::with(['items.unit', 'items.subItems.unit'])->findOrFail($id);

        $qrString = "SJNID:{$id}";
        $qrCode = base64_encode(QrCode::format('svg')->size(200)->errorCorrection('H')->generate($qrString));

        // Sanitasi nama file untuk menghindari error InvalidArgumentException
        // Ganti karakter / dan \ dengan underscore
        $sanitizedDocNumber = preg_replace('/[\/\\\\]/', '_', $travelDocument->no_travel_document);

        $pdf = PDF::loadView('General.shippings-print', compact('travelDocument', 'qrCode'));
        return $pdf->stream("SJN_{$sanitizedDocNumber}.pdf");
    }

    /**
     * Export shippings to Excel
     */
    public function exportShippings(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $filename = 'pengiriman_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new ShippingsExport($request->start_date, $request->end_date), $filename);
    }

    /**
     * Print Delivery Confirmation Report as PDF
     */
    public function printReport($id)
    {
        $travelDocument = TravelDocument::with(['driver', 'deliveryConfirmation.photos'])->findOrFail($id);

        // Optional: hanya cetak jika status terkirim
        if ($travelDocument->status !== 'Terkirim') {
            return redirect()->route('shippings.detail', $id)->with('error', 'Bukti pengiriman hanya bisa dicetak jika status sudah Terkirim.');
        }

        $confirmation = $travelDocument->deliveryConfirmation;

        // Ambil foto dari relasi photos, fallback ke kolom photo_path jika relasi kosong
        $photos = [];
        if ($confirmation) {
            $photos = $confirmation->photos->pluck('photo_path')->filter()->values()->toArray();

            if (empty($photos) && !empty($confirmation->photo_path)) {
                $photos = [$confirmation->photo_path];
            }
        }

        // nama file aman (hindari / \)
        $sanitized = preg_replace('/[\/\\\\]/', '_', $travelDocument->no_travel_document ?? 'SJN');

        $pdf = PDF::loadView('General.shippings-print-report', [
            'travelDocument' => $travelDocument,
            'confirmation' => $confirmation,
            'photos' => $photos,
            'printedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("Bukti_Pengiriman_{$sanitized}.pdf");
    }

    // ========================================
    // TRACKING
    // ========================================

    /**
     * Show tracking page
     */
    public function track(Request $request)
    {
        if ($request->has(['status', 'message'])) {
            session()->flash($request->status, $request->message);
        }
        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Tracking Pengiriman', 'url' => '#']];
        return view('General.tracker', compact('breadcrumbs'));
    }

    /**
     * Show tracker with specific track_id
     */
    public function showTracker($track_id)
    {
        $trackingSystem = TrackingSystem::with('track')->where('track_id', $track_id)->firstOrFail();

        $locations = TrackingSystem::where('track_id', $track_id)->get(['latitude', 'longitude']);

        $initialLocation = $locations->isNotEmpty() ? [$locations->first()->latitude, $locations->first()->longitude] : [0, 0];

        return view('General.tracker', compact('trackingSystem', 'locations', 'initialLocation', 'breadcrumbs'));
    }

    /**
     * Search tracking by travel document number
     */
    public function search(Request $request)
    {
        $noTravelDocument = $request->query('no_travel_document');

        $travelDocument = TravelDocument::where('no_travel_document', $noTravelDocument)
            ->with(['trackingSystems.track.locations'])
            ->first();

        if (!$travelDocument) {
            return $this->trackingErrorResponse('Travel Document tidak ditemukan');
        }

        $locations = $this->extractLocations($travelDocument);

        if (empty($locations)) {
            return $this->trackingErrorResponse('Lokasi tidak ditemukan');
        }

        return response()->json([
            'success' => true,
            'locations' => $locations,
        ]);
    }

    // ========================================
    // HELPER METHODS (PRIVATE)
    // ========================================

    /**
     * Validate travel document request
     */
    private function validateTravelDocument(Request $request, bool $enforceUniqueNumberSjn = false)
    {
        $attributes = $this->getValidationAttributes($request);
        $rules = $this->getValidationRules($enforceUniqueNumberSjn);
        $messages = $this->getValidationMessages();

        return Validator::make($request->all(), $rules, $messages, $attributes);
    }

    private function syncAttachments(TravelDocument $travelDocument, array $attachments, bool $replace = true): void
    {
        $clean = collect($attachments)->map(fn($v) => trim((string) $v))->filter(fn($v) => $v !== '')->values()->all();

        if ($replace) {
            $travelDocument->attachments()->delete();
        }

        if (!empty($clean)) {
            $travelDocument->attachments()->createMany(array_map(fn($name) => ['name' => $name], $clean));
        }
    }

    /**
     * Get validation rules
     */
    private function getValidationRules(bool $enforceUniqueNumberSjn = false): array
    {
        $rules = [
            'sendTo' => 'required|string|max:255',
            'numberSJN' => 'required|string|max:100',
            'numberRef' => 'required|string|max:100',
            'referenceDate' => 'nullable|date',
            'deliveryType' => 'required|in:Dalam Kota,Luar Kota',
            'projectName' => 'required|string|max:255',
            'poNumber' => 'required|string|max:100',
            'documentDate' => 'nullable|date', // Validasi untuk document_date
            'itemCode' => 'required|array|min:1',
            'itemCode.*' => 'required|string|max:100',
            'itemName' => 'required|array|min:1',
            'itemName.*' => 'required|string|max:1000',
            'quantitySend' => 'nullable|array',
            'quantitySend.*' => 'nullable|string|max:50',
            'totalSend' => 'required|array|min:1',
            'totalSend.*' => 'required|string|max:50',
            'qtyPreOrder' => 'nullable|array',
            'qtyPreOrder.*' => 'nullable|string|max:50',
            'unitType' => 'required|array|min:1',
            'unitType.*' => 'required|exists:units,id',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:1000',
            'information' => 'nullable|array',
            'information.*' => 'nullable|string',
            // nomer barang
            'no' => 'nullable|array',
            'no.*' => 'nullable|string|max:100',
            // ===== Attachments =====
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|string|max:255',
            // ===== Vehicle & Driver =====
            'vehicleNumber' => 'nullable|string|max:20',
            'driverName' => 'nullable|string|max:255',
            // ===== Sub Items =====
            'subItemGroupTitle' => 'nullable|array',
            'subItemGroupTitle.*' => 'nullable|string|max:255',
            'subItems' => 'nullable|array',
            'subItems.*' => 'nullable|array',
            'subItems.*.*.item_code' => 'required_with:subItems.*.*|string|max:100',
            'subItems.*.*.item_name' => 'required_with:subItems.*.*|string|max:255',
            'subItems.*.*.qty_send' => 'nullable|string|max:50',
            'subItems.*.*.total_send' => 'required_with:subItems.*.*|string|max:50',
            'subItems.*.*.qty_po' => 'nullable|string|max:50',
            'subItems.*.*.unit_id' => 'required_with:subItems.*.*|exists:units,id',
            'subItems.*.*.description' => 'nullable|string|max:1000',
            'subItems.*.*.information' => 'nullable|string',
        ];

        if ($enforceUniqueNumberSjn) {
            $rules['numberSJN'] .= '|unique:travel_document,no_travel_document';
        }

        return $rules;
    }

    /**
     * Get validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'sendTo.required' => 'Tujuan pengiriman harus diisi. ',
            'numberSJN.required' => 'Nomor SJN harus diisi.',
            'numberSJN.unique' => 'Nomor SJN sudah digunakan. Gunakan nomor yang berbeda.',
            'numberRef.required' => 'Nomor referensi harus diisi.',
            'referenceDate.date' => 'Format tanggal referensi tidak valid.',
            'deliveryType.required' => 'Jenis pengiriman harus dipilih.',
            'deliveryType.in' => 'Jenis pengiriman tidak valid.',
            'projectName.required' => 'Nama proyek harus diisi.',
            'poNumber.required' => 'Nomor PO harus diisi.',
            'documentDate.date' => 'Format tanggal dokumen tidak valid.',
            'itemCode.*.required' => ':attribute harus diisi.',
            'itemName.*.required' => ':attribute harus diisi.',
            'totalSend.*.required' => ':attribute harus diisi.',

            'totalSend.*.max'    => ':attribute maksimal 50 karakter.',
            'quantitySend.*.max' => ':attribute maksimal 50 karakter.',
            'qtyPreOrder.*.string' => ':attribute harus berupa teks.',
            'qtyPreOrder.*.max' => ':attribute maksimal 50 karakter.',
            'description.*.max' => ':attribute maksimal 1000 karakter.',
            'unitType.*.required' => ':attribute harus diisi.',
            'unitType.*.exists' => ':attribute tidak valid.',
            'driverName.max' => 'Nama driver maksimal 255 karakter.',
            'vehicleNumber.max' => 'Nopol maksimal 20 karakter.',
        ];
    }

    /**
     * Get custom validation attributes
     */
    private function getValidationAttributes(Request $request): array
    {
        $attributes = [];
        $fields = [
            'itemCode' => 'Kode barang',
            'itemName' => 'Nama barang',
            'no' => 'No',
            'quantitySend' => 'Jumlah kirim',
            'totalSend' => 'Total kirim',
            'qtyPreOrder' => 'Qty PO',
            'unitType' => 'Satuan',
            'description' => 'Deskripsi',
            'information' => 'Informasi',
        ];

        foreach ($fields as $field => $label) {
            foreach ($request->input($field, []) as $key => $value) {
                $attributes["{$field}.{$key}"] = "{$label} baris " . ($key + 1);
            }
        }

        $attributes['driverName'] = 'Nama driver';
        $attributes['vehicleNumber'] = 'No polisi';

        return $attributes;
    }

    /**
     * Create travel document
     */
    private function createTravelDocument(array $validated): TravelDocument
    {
        // Posting date selalu menggunakan tanggal hari ini
        $postingDate = now();

        // Document date bisa dari input atau default ke posting date
        if (isset($validated['documentDate']) && !empty($validated['documentDate'])) {
            $documentDate = \Carbon\Carbon::parse($validated['documentDate']);
        } else {
            $documentDate = $postingDate->copy();
        }

        // Tentukan apakah backdate dengan membandingkan string tanggal
        $postingDateStr = $postingDate->format('Y-m-d');
        $documentDateStr = $documentDate->format('Y-m-d');
        $isBackdate = $documentDateStr !== $postingDateStr;

        return TravelDocument::create([
            'no_travel_document' => $validated['numberSJN'],
            'posting_date' => $postingDate,
            'document_date' => $documentDate,
            'is_backdate' => $isBackdate,
            'send_to' => $validated['sendTo'],
            'reference_number' => $validated['numberRef'],
            'reference_date' => $validated['referenceDate'] ?? null,
            'po_number' => $validated['poNumber'],
            'project' => $validated['projectName'],
            'delivery_type' => $validated['deliveryType'],
            'driver_name' => $validated['driverName'] ?? null,
            'vehicle_number' => $validated['vehicleNumber'] ?? null,
            'status' => 'Belum terkirim',
        ]);
    }

    /**
     * Update travel document
     */
    private function updateTravelDocument(TravelDocument $travelDocument, array $validated): void
    {
        // Posting date selalu menggunakan tanggal hari ini saat update
        $postingDate = Carbon::parse($travelDocument->posting_date);

        // Document date bisa dari input atau default ke posting date
        if (isset($validated['documentDate']) && !empty($validated['documentDate'])) {
            $documentDate = \Carbon\Carbon::parse($validated['documentDate']);
        } else {
            $documentDate = $postingDate->copy();
        }

        // Tentukan apakah backdate dengan membandingkan string tanggal
        $postingDateStr = $postingDate->format('Y-m-d');
        $documentDateStr = $documentDate->format('Y-m-d');
        $isBackdate = $documentDateStr !== $postingDateStr;

        $travelDocument->update([
            'no_travel_document' => $validated['numberSJN'],
            // 'posting_date' => $postingDate,
            'document_date' => $documentDate,
            'is_backdate' => $isBackdate,
            'send_to' => $validated['sendTo'],
            'reference_number' => $validated['numberRef'],
            'reference_date' => $validated['referenceDate'] ?? null,
            'po_number' => $validated['poNumber'],
            'project' => $validated['projectName'],
            'delivery_type' => $validated['deliveryType'],
            'driver_name' => $validated['driverName'] ?? null,
            'vehicle_number' => $validated['vehicleNumber'] ?? null,
            // 'status' => 'Belum terkirim',
        ]);
    }

    /**
     * Create travel document items
     */
    private function createTravelDocumentItems(TravelDocument $travelDocument, array $validated): void
    {
        foreach ($validated['itemCode'] as $idx => $itemCode) {
            // qty_po (parent item) bisa int / string / '-'
            $qtyPo = $validated['qtyPreOrder'][$idx] ?? null;

            if ($qtyPo === null || $qtyPo === '' || trim((string) $qtyPo) === '') {
                $qtyPo = null;
            } elseif ($qtyPo === '-' || !is_numeric($qtyPo)) {
                $qtyPo = trim((string) $qtyPo);
            } else {
                $qtyPo = (int) $qtyPo;
            }

            // Create parent item (butuh ID untuk sub items)
            $item = $travelDocument->items()->create([
                'item_code' => $itemCode,
                'item_name' => $validated['itemName'][$idx],
                'no' => $validated['no'][$idx] ?? null,
                'qty_send' => $validated['quantitySend'][$idx] ?? null,
                'total_send' => $validated['totalSend'][$idx],
                'qty_po' => $qtyPo,
                'unit_id' => $validated['unitType'][$idx],
                'description' => $this->normalizeDescription($validated['description'][$idx] ?? null),
                'information' => $validated['information'][$idx] ?? null,
                'sub_item_group_title' => $validated['subItemGroupTitle'][$idx] ?? null,
            ]);

            // ===== Sub Items =====
            $subRows = $validated['subItems'][$idx] ?? [];

            $cleanSub = collect($subRows)
                ->filter(fn($row) => is_array($row))
                ->map(function ($row) {
                    $desc = trim((string) ($row['description'] ?? ''));

                    $qtyPo = $row['qty_po'] ?? null;
                    $qtyPo = $qtyPo === null || trim((string) $qtyPo) === '' ? null : (string) $qtyPo;

                    return [
                        'item_code' => trim((string) ($row['item_code'] ?? '')),
                        'item_name' => trim((string) ($row['item_name'] ?? '')),
                        'qty_send' => $row['qty_send'] ?? null,
                        'total_send' => $row['total_send'] ?? null,
                        'qty_po' => $qtyPo,
                        'unit_id' => (int) ($row['unit_id'] ?? 0),
                        'description' => $desc === '' ? '-' : $desc,
                        'information' => $row['information'] ?? null,
                    ];
                })
                // anggap baris kosong = tidak disimpan
                ->filter(fn($r) => $r['item_code'] !== '' && $r['item_name'] !== '')
                ->values()
                ->all();

            if (!empty($cleanSub)) {
                $item->subItems()->createMany($cleanSub);
            }
        }
    }

    /**
     * Normalize item description to satisfy non-null DB constraint.
     */
    private function normalizeDescription(?string $description): string
    {
        $normalized = trim((string) $description);

        return $normalized === '' ? '-' : $normalized;
    }

    /**
     * Handle shipping error consistently and provide user-friendly feedback.
     */
    private function handleShippingError(\Throwable $exception, string $userMessage, array $context = [])
    {
        Log::error(
            'Shipping operation failed.',
            array_merge($context, [
                'exception_message' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]),
        );

        return redirect()
            ->back()
            ->with('error', $userMessage . ' Silakan coba lagi atau hubungi admin jika masalah berlanjut.');
    }

    /**
     * Extract locations from travel document
     */
    private function extractLocations(TravelDocument $travelDocument): array
    {
        $locations = [];

        foreach ($travelDocument->trackingSystems as $trackingSystem) {
            foreach ($trackingSystem->track->locations as $location) {
                $locations[] = [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                ];
            }
        }

        return $locations;
    }

    /**
     * Return tracking error response
     */
    private function trackingErrorResponse(string $message)
    {
        if (request()->expectsJson()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $message,
                ],
                404,
            );
        }

        return redirect()->back()->with('error', $message);
    }

    /**
     * Display trashed travel documents
     */
    public function shippingsTrash()
    {
        $trashedDocuments = TravelDocument::onlyTrashed()->with('items')->orderBy('deleted_at', 'desc')->paginate(10);

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')], ['label' => 'Trash', 'url' => '#']];

        return view('General.shippings-trash', compact('trashedDocuments', 'breadcrumbs'));
    }

    /**
     * Restore a trashed travel document
     */
    public function shippingsRestore($id)
    {
        $travelDocument = TravelDocument::withTrashed()->findOrFail($id);
        $travelDocument->restore();

        return redirect()->route('shippings.trash')->with('success', 'Data pengiriman berhasil direstore.');
    }

    public function shippingsReport($id)
    {
        $travelDocument = TravelDocument::findOrFail($id);
        $confirmation = DeliveryConfirmation::where('travel_document_id', $id)->first();
        $tracking = TrackingSystem::where('travel_document_id', $id)->with('track.locations')->first();
        $startTime = null;
        $endTime = null;

        if ($tracking && $tracking->track && $tracking->track->locations->isNotEmpty()) {
            $startTime = $tracking->track->locations->first()->created_at;
            $endTime = $tracking->track->locations->last()->created_at;
        }

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')], ['label' => 'Shipping Report', 'url' => '#']];

        return view('General.shippings-report', compact('travelDocument', 'confirmation', 'startTime', 'endTime', 'breadcrumbs'));
    }
}
