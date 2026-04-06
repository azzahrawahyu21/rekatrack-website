<?php

namespace App\Exports;

use App\Models\TravelDocument;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ShippingsExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize,
    WithEvents
{
    protected $startDate;
    protected $endDate;

    // 1 SJN = 1 NO
    private int $sjnNo = 1;

    // range row per SJN buat merge
    private array $docRowRanges = [];
    private int $excelRowPointer = 2; // header row 1, data mulai row 2

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function collection()
    {
        $query = TravelDocument::with([
                'items.unit',
                'items.subItems.unit',
                'driver',
            ])
            ->orderBy('posting_date', 'desc')
            ->orderBy('created_at', 'desc');

        $this->applyDateFilters($query);

        $docs = $query->get();

        $rows = new Collection();

        foreach ($docs as $doc) {
            $startRow = $this->excelRowPointer;
            $isFirstRowOfDoc = true;

            // tetap tampil walau items kosong
            if ($doc->items->isEmpty()) {
                $rows->push($this->mapEmptyDocument($doc, $isFirstRowOfDoc));
                $this->excelRowPointer++;

                $endRow = $this->excelRowPointer - 1;
                $this->docRowRanges[] = [$startRow, $endRow];
                $this->sjnNo++;
                continue;
            }

            foreach ($doc->items as $index => $item) {
                // ITEM
                $rows->push($this->mapItemRow($doc, $item, $index, $isFirstRowOfDoc));
                $this->excelRowPointer++;
                $isFirstRowOfDoc = false;

                // SUB ITEM (di bawah item)
                if ($item->subItems && $item->subItems->isNotEmpty()) {
                    foreach ($item->subItems as $sub) {
                        $rows->push($this->mapSubItemRow($doc, $sub));
                        $this->excelRowPointer++;
                    }
                }
            }

            $endRow = $this->excelRowPointer - 1;
            $this->docRowRanges[] = [$startRow, $endRow];
            $this->sjnNo++;
        }

        return $rows;
    }

    /**
     * URUTAN KOLOM FINAL:
     * A NO
     * B TANGGAL SJN
     * C NOMOR SJN
     * D KEPADA
     * E PROYEK
     * F NO ITEM
     * G NAMA BARANG
     * H KODE BARANG
     * I QTY KIRIM
     * J TOTAL KIRIM
     * K QTY PO
     * L SATUAN
     * M KETERANGAN
     * N NOMOR REFERENSI
     * O TANGGAL REFERENSI
     * P NO PO
     * Q TANGGAL KIRIM
     * R TANGGAL DITERIMA
     * S PENGIRIM
     * T STATUS
     */
    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL SJN',
            'NOMOR SJN',
            'KEPADA',
            'PROYEK',
            'NO ITEM',
            'NAMA BARANG',
            'KODE BARANG',
            'QTY KIRIM',
            'TOTAL KIRIM',
            'QTY PO',
            'SATUAN',
            'KETERANGAN',
            'NOMOR REFERENSI',
            'TANGGAL REFERENSI',
            'NO PO',
            'TANGGAL KIRIM',
            'TANGGAL DITERIMA',
            'PENGIRIM',
            'STATUS',
        ];
    }

    /**
     * Header style (warna)
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:T1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'], // biru header
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Merge vertikal per SJN (kolom dokumen)
                // A NO, B TGL SJN, C NOMOR SJN, D KEPADA, E PROYEK,
                // N NOMOR REF, O TGL REF, P NO PO,
                // S PENGIRIM, T STATUS
                $mergeCols = ['A', 'B', 'C', 'D', 'E', 'N', 'O', 'P', 'S', 'T'];

                foreach ($this->docRowRanges as [$startRow, $endRow]) {
                    if ($endRow <= $startRow) continue;

                    foreach ($mergeCols as $col) {
                        $event->sheet->mergeCells("{$col}{$startRow}:{$col}{$endRow}");
                        $event->sheet->getStyle("{$col}{$startRow}")
                            ->getAlignment()
                            ->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }

                // Warna status (kolom T)
                $highestRow = $event->sheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $cell = "T{$row}";
                    $status = trim((string) $event->sheet->getCell($cell)->getValue());

                    if ($status === 'Terkirim') {
                        $event->sheet->getStyle($cell)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'C6EFCE'], // hijau soft
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => '006100'],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    } elseif ($status === 'Belum terkirim') {
                        $event->sheet->getStyle($cell)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8CBAD'], // merah soft
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => '9C0006'],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    } elseif ($status === 'Sedang dikirim') {
                        $event->sheet->getStyle($cell)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'BDD7EE'], // biru soft
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => '1F4E78'],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }
                }

                // Rapihin
                $event->sheet->freezePane('A2');
                $event->sheet->setAutoFilter('A1:T1');
            },
        ];
    }

    // =========================
    // Row mapping (sesuai urutan final)
    // =========================

    private function mapEmptyDocument($doc, bool $isFirstRowOfDoc): array
    {
        return [
            $isFirstRowOfDoc ? $this->sjnNo : '',
            $this->formatDate($doc->posting_date),
            $doc->no_travel_document ?? '',
            $doc->send_to ?? '',
            $doc->project ?? '',
            '', // NO ITEM
            '', // NAMA BARANG
            '', // KODE BARANG
            '', // QTY KIRIM
            '', // TOTAL KIRIM
            '', // QTY PO
            '', // SATUAN
            '', // KETERANGAN
            $doc->reference_number ?? '',
            $this->formatDate($doc->reference_date),
            $doc->po_number ?? '',
            $doc->start_time ? $this->formatDateTime($doc->start_time) : '',
            $doc->end_time ? $this->formatDateTime($doc->end_time) : '',
            $this->getSender($doc),
            $doc->status ?? '',
        ];
    }

    private function mapItemRow($doc, $item, int $index, bool $isFirstRowOfDoc): array
    {
        return [
            $isFirstRowOfDoc ? $this->sjnNo : '',
            $this->formatDate($doc->posting_date),
            $doc->no_travel_document ?? '',
            $doc->send_to ?? '',
            $doc->project ?? '',
            $this->resolveItemNo($item, $index),
            $item->item_name ?? '', // NAMA BARANG
            $item->item_code ?? '', // KODE BARANG
            $item->qty_send ?? 0,
            $item->total_send ?? 0,
            $item->qty_po ?? 0,
            $item->unit?->name ?? '',
            $item->information ?? '',
            $doc->reference_number ?? '',
            $this->formatDate($doc->reference_date),
            $doc->po_number ?? '',
            $doc->start_time ? $this->formatDateTime($doc->start_time) : '',
            $doc->end_time ? $this->formatDateTime($doc->end_time) : '',
            $this->getSender($doc),
            $doc->status ?? '',
        ];
    }

    private function mapSubItemRow($doc, $sub): array
    {
        return [
            '', // NO kosong (1 SJN 1 NO)
            $this->formatDate($doc->posting_date),
            $doc->no_travel_document ?? '',
            $doc->send_to ?? '',
            $doc->project ?? '',
            '', // NO ITEM kosong untuk sub item
            $sub->item_name ?? '', // NAMA BARANG
            $sub->item_code ?? '', // KODE BARANG
            $sub->qty_send ?? 0,
            $sub->total_send ?? 0,
            $sub->qty_po ?? 0,
            $sub->unit?->name ?? '',
            $sub->information ?? '',
            $doc->reference_number ?? '',
            $this->formatDate($doc->reference_date),
            $doc->po_number ?? '',
            $doc->start_time ? $this->formatDateTime($doc->start_time) : '',
            $doc->end_time ? $this->formatDateTime($doc->end_time) : '',
            $this->getSender($doc),
            $doc->status ?? '',
        ];
    }

    // =========================
    // Helpers
    // =========================

    private function applyDateFilters($query): void
    {
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('posting_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        } elseif ($this->startDate) {
            $query->where('posting_date', '>=', Carbon::parse($this->startDate)->startOfDay());
        } elseif ($this->endDate) {
            $query->where('posting_date', '<=', Carbon::parse($this->endDate)->endOfDay());
        }
    }

    private function resolveItemNo($item, int $index): string
    {
        $val = $item->no ?? null;
        if ($val !== null && trim((string) $val) !== '') return (string) $val;
        return (string) ($index + 1);
    }

    private function getSender($doc): string
    {
        // sesuai blade kamu:
        // {{ $travelDocument->driver->name }} ({{ $travelDocument->driver->nip }})
        if (isset($doc->driver) && $doc->driver) {
            $name = $doc->driver->name ?? '';
            $nip  = $doc->driver->nip ?? '';
            if ($name !== '' && $nip !== '') return "{$name} ({$nip})";
            return $name !== '' ? $name : '-';
        }
        return '-';
    }

    private function formatDate($date): string
    {
        if (!$date) return '';
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return '';
        }
    }

    private function formatDateTime($dateTime): string
    {
        if (!$dateTime) return '';
        try {
            return Carbon::parse($dateTime)->format('Y-m-d H:i');
        } catch (\Exception $e) {
            return '';
        }
    }
}