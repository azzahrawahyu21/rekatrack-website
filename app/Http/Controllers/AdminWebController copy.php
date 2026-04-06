<?php

namespace App\Http\Controllers;

use App\Models\TrackingSystem;
use App\Models\TravelDocument;
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

class AdminWebController extends Controller
{
    // ========================================
    // SHIPPING MANAGEMENT
    // ========================================

    /**
     * Display list of travel documents
     */
    public function shippingsIndex()
    {
        // Ambil data untuk pagination
        $listTravelDocument = TravelDocument::latest('id')->paginate(10);

        // Hitung total global per status (semua data, bukan hanya halaman ini)
        $stats = TravelDocument::selectRaw(
            "
            SUM(CASE WHEN status = 'Belum terkirim' THEN 1 ELSE 0 END) as belum_terkirim,
            SUM(CASE WHEN status = 'Sedang dikirim' THEN 1 ELSE 0 END) as sedang_dikirim,
            SUM(CASE WHEN status = 'Terkirim' THEN 1 ELSE 0 END) as terkirim,
            COUNT(*) as total
        ",
        )->first();

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')], ['label' => 'Manajemen Pengiriman', 'url' => '#']];

        // Kirim ke view
        return view('General.shippings', [
            'listTravelDocument' => $listTravelDocument,
            'totalPengiriman' => $stats->total,
            'totalBelumTerkirim' => $stats->belum_terkirim,
            'totalSedangDikirim' => $stats->sedang_dikirim,
            'totalTerkirim' => $stats->terkirim,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Search travel documents by multiple criteria
     */
    public function searchDocument(Request $request)
    {
        $query = $request->input('search');

        $results = TravelDocument::query()
            ->where(function ($q) use ($query) {
                $q->where('no_travel_document', 'like', "%{$query}%")
                    ->orWhere('send_to', 'like', "%{$query}%")
                    ->orWhere('status', 'like', "%{$query}%")
                    ->orWhere('project', 'like', "%{$query}%");
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
        $travelDocument = TravelDocument::with(['items.unit'])->findOrFail($id);

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')],
                        ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')],
                        ['label' => 'Detail Pengiriman', 'url' => '#']];

        return view('General.shippings-detail', compact('travelDocument', 'breadcrumbs'));
    }

    /**
     * Show form to add new travel document
     */
    public function shippingsAdd()
    {
        $units = Unit::all();
        $items = [['itemCode' => '', 'itemName' => '', 'quantitySend' => '', 'unitType' => '', 'description' => '', 'totalSend' => '', 'information' => '', 'qtyPreOrder' => '']];

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')],
                        ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')],
                        ['label' => 'Tambah Pengiriman', 'url' => '#']];

        return view('General.shippings-add', compact('units', 'items', 'breadcrumbs'));
    }

    /**
     * Show form to edit travel document
     */
    public function shippingsEdit($id)
    {
        $travelDocument = TravelDocument::with('items')->findOrFail($id);
        $units = Unit::all();

        // Siapkan items untuk view
        $items = [];

        // Jika ada old input (setelah redirect back), pakai old()
        if (old('itemName')) {
            $count = count(old('itemName'));
            for ($i = 0; $i < $count; $i++) {
                $items[] = [
                    'itemName' => old("itemName.$i"),
                    'itemCode' => old("itemCode.$i"),
                    'quantitySend' => old("quantitySend.$i"),
                    'unitType' => old("unitType.$i"),
                    'description' => old("description.$i"),
                    'totalSend' => old("totalSend.$i"),
                    'information' => old("information.$i"),
                    'qtyPreOrder' => old("qtyPreOrder.$i"),
                ];
            }
        } else {
            // Jika tidak, ambil dari database
            foreach ($travelDocument->items as $item) {
                $items[] = [
                    'itemName' => $item->item_name,
                    'itemCode' => $item->item_code,
                    'quantitySend' => $item->qty_send,
                    'unitType' => $item->unit_id,
                    'description' => $item->description,
                    'totalSend' => $item->total_send,
                    'information' => $item->information,
                    'qtyPreOrder' => $item->qty_po,
                ];
            }
        }

        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')],
                        ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')],
                        ['label' => 'Edit Pengiriman', 'url' => '#']];

        return view('General.shippings-edit', compact('travelDocument', 'units', 'items', 'breadcrumbs'));
    }

    /**
     * Store new travel document with items
     */
    public function shippingsAddTravelDocument(Request $request)
    {
        $validator = $this->validateTravelDocument($request);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $travelDocument = $this->createTravelDocument($validated);
            $this->createTravelDocumentItems($travelDocument, $validated);

            DB::commit();
            return redirect()->route('shippings.index')->with('success', 'Data pengiriman berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
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

            DB::commit();
            return redirect()->route('shippings.index')->with('success', 'Data pengiriman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Save As - Create new travel document from existing one
     */
    public function shippingsSaveAs(Request $request, $id)
    {
        // Validasi dengan pengecekan unique untuk no_travel_document
        $validator = Validator::make($request->all(), array_merge(
            $this->getValidationRules(),
            [
                'numberSJN' => 'required|string|max:100|unique:travel_document,no_travel_document',
            ]
        ), array_merge(
            $this->getValidationMessages(),
            [
                'numberSJN.unique' => 'Nomor SJN sudah digunakan. Gunakan nomor yang berbeda.',
            ]
        ), $this->getValidationAttributes($request));

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            // Cek sekali lagi untuk memastikan nomor dokumen belum ada
            $exists = TravelDocument::where('no_travel_document', $validated['numberSJN'])->exists();

            if ($exists) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Nomor SJN sudah digunakan. Gunakan nomor yang berbeda.');
            }

            // Buat dokumen baru (bukan update yang lama)
            $newTravelDocument = $this->createTravelDocument($validated);
            $this->createTravelDocumentItems($newTravelDocument, $validated);

            DB::commit();
            return redirect()
                ->route('shippings.index')
                ->with('success', 'Data pengiriman berhasil disimpan sebagai dokumen baru dengan nomor: ' . $validated['numberSJN']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
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
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
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
        $travelDocument = TravelDocument::with('items.unit')->findOrFail($id);

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
        $breadcrumbs = [['label' => 'Home', 'url' => route('shippings.index')],
                        ['label' => 'Tracking Pengiriman', 'url' => '#']];
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
    private function validateTravelDocument(Request $request)
    {
        $attributes = $this->getValidationAttributes($request);
        $rules = $this->getValidationRules();
        $messages = $this->getValidationMessages();

        return Validator::make($request->all(), $rules, $messages, $attributes);
    }

    /**
     * Get validation rules
     */
    private function getValidationRules(): array
    {
        return [
            'sendTo' => 'required|string|max:255',
            'numberSJN' => 'required|string|max:100',
            'numberRef' => 'required|string|max:100',
            'projectName' => 'required|string|max:255',
            'poNumber' => 'required|string|max:100',
            'itemCode.*' => 'required|string|max:100',
            'itemName.*' => 'required|string|max:255',
            'quantitySend.*' => 'nullable|integer|min:0',
            'totalSend.*' => 'required|integer|min:0',
            // FIX: Ubah validation untuk qtyPreOrder agar bisa terima string (untuk symbol '-')
            'qtyPreOrder.*' => 'nullable|string|max:50',  // Ubah dari integer menjadi string
            'unitType.*' => 'required|exists:units,id',
            'description.*' => 'required|string',
            'information.*' => 'nullable|string',
        ];
    }

    /**
     * Get validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'sendTo.required' => 'Tujuan pengiriman harus diisi.',
            'numberSJN.required' => 'Nomor SJN harus diisi.',
            'numberRef.required' => 'Nomor referensi harus diisi.',
            'projectName.required' => 'Nama proyek harus diisi.',
            'poNumber.required' => 'Nomor PO harus diisi.',
            'itemCode.*.required' => ':attribute harus diisi.',
            'itemName.*.required' => ':attribute harus diisi.',
            'totalSend.*.required' => ':attribute harus diisi.',
            'totalSend.*.integer' => ':attribute harus berupa angka.',
            'totalSend.*.min' => ':attribute minimal 0.',
            'quantitySend.*.integer' => ':attribute harus berupa angka.',
            'quantitySend.*.min' => ':attribute minimal 0.',
            // Update message untuk qtyPreOrder
            'qtyPreOrder.*.string' => ':attribute harus berupa teks.',
            'qtyPreOrder.*.max' => ':attribute maksimal 50 karakter.',
            'unitType.*.required' => ':attribute harus diisi.',
            'unitType.*.exists' => ':attribute tidak valid.',
            'description.*.required' => ':attribute harus diisi.',
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

        return $attributes;
    }

    /**
     * Create travel document
     */
    private function createTravelDocument(array $validated): TravelDocument
    {
        return TravelDocument::create([
            'no_travel_document' => $validated['numberSJN'],
            'date_no_travel_document' => now(),
            'send_to' => $validated['sendTo'],
            'reference_number' => $validated['numberRef'],
            'po_number' => $validated['poNumber'],
            'project' => $validated['projectName'],
            'status' => 'Belum terkirim',
        ]);
    }

    /**
     * Update travel document
     */
    private function updateTravelDocument(TravelDocument $travelDocument, array $validated): void
    {
        $travelDocument->update([
            'no_travel_document' => $validated['numberSJN'],
            'send_to' => $validated['sendTo'],
            'reference_number' => $validated['numberRef'],
            'po_number' => $validated['poNumber'],
            'project' => $validated['projectName'],
            'status' => 'Belum terkirim',
        ]);
    }

    /**
     * Create travel document items
     * FIX: Handle qtyPreOrder yang bisa berupa string (untuk symbol '-')
     */
    private function createTravelDocumentItems(TravelDocument $travelDocument, array $validated): void
    {
        $items = [];

        foreach ($validated['itemCode'] as $key => $itemCode) {
            // Ambil qty_po, jika kosong atau '-' set sebagai null atau 0
            $qtyPo = $validated['qtyPreOrder'][$key] ?? null;

            // Jika qtyPo adalah '-' atau string non-numeric, simpan sebagai string
            // Jika numeric, convert ke integer
            // Jika null/empty, set sebagai null
            if ($qtyPo === null || $qtyPo === '' || trim($qtyPo) === '') {
                $qtyPo = null;
            } elseif ($qtyPo === '-' || !is_numeric($qtyPo)) {
                // Simpan sebagai string (untuk symbol seperti '-')
                $qtyPo = trim($qtyPo);
            } else {
                // Convert ke integer jika numeric
                $qtyPo = (int) $qtyPo;
            }

            $items[] = [
                'travel_document_id' => $travelDocument->id,
                'item_code' => $itemCode,
                'item_name' => $validated['itemName'][$key],
                'qty_send' => $validated['quantitySend'][$key] ?? 0,
                'total_send' => (int) $validated['totalSend'][$key],
                'qty_po' => $qtyPo,  // Bisa integer, string ('-'), atau null
                'unit_id' => $validated['unitType'][$key],
                'description' => $validated['description'][$key],
                'information' => $validated['information'][$key] ?? null,
            ];
        }

        $travelDocument->items()->createMany($items);
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
        $trashedDocuments = TravelDocument::onlyTrashed()
            ->with('items')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('shippings.index')],
            ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')],
            ['label' => 'Trash', 'url' => '#']
        ];

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

    public function shippingsReport($id) {
        $travelDocument = TravelDocument::findOrFail($id);
        $confirmation = DeliveryConfirmation::where('travel_document_id', $id)->first();
        $tracking = TrackingSystem::where('travel_document_id', $id)
            ->with('track.locations')
            ->first();
        $startTime = null;
        $endTime = null;

        if ($tracking && $tracking->track && $tracking->track->locations->isNotEmpty()) {
            $startTime = $tracking->track->locations->first()->created_at;
            $endTime   = $tracking->track->locations->last()->created_at;
        }

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('shippings.index')],
            ['label' => 'Manajemen Pengiriman', 'url' => route('shippings.index')],
            ['label' => 'Shipping Report', 'url' => '#'],
        ];

        return view('General.shippings-report', compact(
            'travelDocument',
            'confirmation',
            'startTime',
            'endTime',
            'breadcrumbs'
        ));
    }
}
