<?php

namespace App\Exports;

use App\Models\TravelDocument;
use App\Models\Items;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ShippingsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    /**
     * Constructor with optional date filters
     */
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Get collection of travel documents with filters
     */
    public function collection()
    {
        $query = TravelDocument::with(['items.unit'])
            ->orderBy('posting_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply date filters
        $this->applyDateFilters($query);

        return $query->get();
    }

    /**
     * Define Excel column headings
     */
    public function headings(): array
    {
        return [
            'No SJN',
            'Tanggal SJN',
            'Kepada',
            'Nomor Referensi',
            'Tanggal Referensi',
            'Nomor PO',
            'Proyek',
            'Status',
            'Kode Barang',
            'Nama Barang',
            'Tanggal Kirim',
            'Tanggal Diterima',
            'Qty Kirim',
            'Qty PO',
            'Total Kirim',
            'Satuan',
            'Keterangan'
        ];
    }

    /**
     * Map travel document data to Excel rows
     */
    public function map($travelDocument): array
    {
        $rows = [];

        if ($travelDocument->items->isEmpty()) {
            $rows[] = $this->mapEmptyDocument($travelDocument);
        } else {
            foreach ($travelDocument->items as $item) {
                $rows[] = $this->mapDocumentWithItem($travelDocument, $item);
            }
        }

        return $rows;
    }

    /**
     * Apply styles to Excel worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style for header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================

    /**
     * Apply date filters to query
     */
    private function applyDateFilters($query): void
    {
        if ($this->startDate && $this->endDate) {
            // Filter by date range
            $query->whereBetween('posting_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        } elseif ($this->startDate) {
            // Filter from start date onwards
            $query->where('posting_date', '>=', Carbon::parse($this->startDate)->startOfDay());
        } elseif ($this->endDate) {
            // Filter up to end date
            $query->where('posting_date', '<=', Carbon::parse($this->endDate)->endOfDay());
        }
    }

    /**
     * Map travel document without items
     */
    private function mapEmptyDocument($travelDocument): array
    {
        return [
            $travelDocument->no_travel_document,
            $this->formatDate($travelDocument->posting_date),
            $travelDocument->send_to,
            $travelDocument->reference_number,
            $this->formatDate($travelDocument->reference_date),
            $travelDocument->po_number,
            $travelDocument->project,
            $travelDocument->status,
            '', // item_code
            '', // item_name
            $travelDocument->start_time ? $this->formatDateTime($travelDocument->start_time) : '',
            $travelDocument->end_time ? $this->formatDateTime($travelDocument->end_time) : '',
            '', // qty_send
            '', // qty_po
            '', // total_send
            '', // unit
            ''  // information
        ];
    }

    /**
     * Map travel document with its item
     */
    private function mapDocumentWithItem($travelDocument, $item): array
    {
        return [
            $travelDocument->no_travel_document,
            $this->formatDate($travelDocument->posting_date),
            $travelDocument->send_to,
            $travelDocument->reference_number,
            $this->formatDate($travelDocument->reference_date),
            $travelDocument->po_number,
            $travelDocument->project,
            $travelDocument->status,
            $item->item_code ?? '',
            $item->item_name ?? '',
            $this->formatDateTime($travelDocument->start_time) ? $this->formatDateTime($travelDocument->start_time) : '',
            $this->formatDateTime($travelDocument->end_time) ? $this->formatDateTime($travelDocument->end_time) : '',
            $item->qty_send ?? 0,
            $item->qty_po ?? 0,
            $item->total_send ?? 0,
            $item->unit?->name ?? '—',
            $item->information ?? ''
        ];
    }

    /**
     * Format date to Y-m-d format
     */
    private function formatDate($date): string
    {
        if (!$date) {
            return '';
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Format datetime to Y-m-d H:i format
     */
    private function formatDateTime($dateTime): string
    {
        if (!$dateTime) {
            return '';
        }

        try {
            return Carbon::parse($dateTime)->format('Y-m-d H:i');
        } catch (\Exception $e) {
            return '';
        }
    }
}
