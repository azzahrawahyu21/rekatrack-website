<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Jalan</title>
    <style>
        @page {
            size: A4;
            margin: 2mm;
        }

        body {
            margin: 5mm;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        .page {
            height: 100%;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .tabel-barang th,
        .tabel-barang td {
            border: 2px solid #000000;
            font-size: 0.75rem;
            padding: 0.5rem 0.25rem;
        }

        .tabel-barang th {
            background-color: #E5E7EB;
            color: #000000;
            text-align: center;
        }

        hr {
            border: none;
            border-top: 2px solid #9CA3AF;
        }

        .info-table td {
            vertical-align: top;
            padding: 0.25rem 0.5rem;
            border: 1px;
        }

        .info-table td:first-child {
            width: 5rem;
            font-weight: 600;
            border: 1px;
        }

        .hidden {
            display: none;
        }

        /* Badge nomor item utama */
        .no-badge {
            font-weight: bold;
            font-size: 0.75rem;
        }

        /* Badge nomor sub item */
        .sub-badge {
            font-size: 0.72rem;

        }

        /* Row sub item */
        .sub-row td {
            background-color: #F9FAFB;

        }

        /* Row group title */
        .group-row td {
            background-color: #F3F4F6;
            font-weight: bold;
            font-size: 0.72rem;
            color: #111827;
            padding: 0.35rem 0.5rem;
        }
    </style>
</head>

<body>
    <div class="page">

        <!-- Header -->
        <table style="width: 100%; margin-bottom: 1rem;">
            <tr>
                <td style="width: 70%; vertical-align: top;">
                    <img src="{{ public_path('images/logo/logo-reka.png') }}" alt="Logo"
                        style="max-width: 180px; margin-bottom: 0.5rem;">
                    <p style="font-size: 1rem; font-weight: bold; margin: 0;">PT Rekaindo Global Jasa</p>
                    <p style="font-size: 0.75rem; margin: 0;">Jl. Candi Sewu No. 30, Madiun 63122</p>
                    <p style="font-size: 0.75rem; margin: 0;">Telp. 0351-4773030</p>
                    <p style="font-size: 0.75rem; margin: 0;">Email: sekretariat@ptrekaindo.co.id</p>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: top;">
                    <img src="data:image/svg+xml;base64, {!! $qrCode !!}" alt="QR Code"
                        style="max-width: 80px; height: auto; margin-top: 0.5rem;">
                </td>
            </tr>
        </table>

        <hr>

        <!-- Judul -->
        <div style="text-align: center; margin: 0.75rem 0;">
            <h2 style="font-size: 1.25rem; font-weight: bold; margin: 0;">SURAT JALAN</h2>
            <p style="font-size: 1.125rem; font-weight: bold; margin: 0;">No: {{ $travelDocument->no_travel_document }}</p>
        </div>

        {{-- info pengiriman --}}
        <table style="width: 100%; margin-bottom: 1rem;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 2rem;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="font-size: 0.75rem; font-weight: bold; width: 60px; padding-bottom: 8px; vertical-align: top;">Proyek</td>
                            <td style="font-size: 0.75rem; width: 10px; padding-bottom: 8px; vertical-align: top;">:</td>
                            <td style="font-size: 0.75rem; padding-bottom: 8px;">{{ $travelDocument->project }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 0.75rem; font-weight: bold; width: 60px; padding-bottom: 8px; vertical-align: top;">Kepada</td>
                            <td style="font-size: 0.75rem; width: 10px; padding-bottom: 8px; vertical-align: top;">:</td>
                            <td style="font-size: 0.75rem; padding-bottom: 8px;">{{ $travelDocument->send_to }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 0.75rem; font-weight: bold; width: 60px; padding-bottom: 8px; vertical-align: top;">Tanggal</td>
                            <td style="font-size: 0.75rem; width: 10px; padding-bottom: 8px; vertical-align: top;">:</td>
                            <td style="font-size: 0.75rem; padding-bottom: 8px;">{{ \Carbon\Carbon::parse($travelDocument->document_date)->format('d/m/Y') }}</td>
                        </tr>
                        {{-- <tr>
                            <td style="font-size: 0.75rem; font-weight: bold; width: 60px; vertical-align: top;">Halaman</td>
                            <td style="font-size: 0.75rem; width: 10px; vertical-align: top;">:</td>
                            <td style="font-size: 0.75rem;">1 dari 1</td>
                        </tr> --}}
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 2rem;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="font-size: 0.75rem; font-weight: bold; width: 40px; padding-bottom: 8px; vertical-align: top;">PO</td>
                            <td style="font-size: 0.75rem; width: 10px; padding-bottom: 8px; vertical-align: top;">:</td>
                            <td style="font-size: 0.75rem; padding-bottom: 8px;">{{ $travelDocument->po_number }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 0.75rem; font-weight: bold; width: 40px; vertical-align: top;">Ref</td>
                            <td style="font-size: 0.75rem; width: 10px; vertical-align: top;">:</td>
                            <td style="font-size: 0.75rem;">{{ $travelDocument->reference_number }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Tabel Barang -->
        <table class="tabel-barang">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Uraian / Deskripsi</th>
                    <th style="width: 12%;">Kode Barang</th>
                    <th style="width: 8%;">Qty Kirim</th>
                    <th style="width: 8%;">Total Kirim</th>
                    <th style="width: 8%;">Qty PO</th>
                    <th style="width: 8%;">Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($travelDocument->items as $index => $item)
                    {{-- Hitung rowspan: 1 (item utama) + 1 (group title) + jumlah sub item --}}
                    @if($item->subItems && $item->subItems->count())
                        {{-- ROW ITEM UTAMA dengan rowspan keterangan --}}
                        <tr>
                            <td style="text-align: center;">
                                <span class="no-badge">{{ $item->no ?? ($index + 1) }}</span>
                            </td>
                            <td style="text-align: left; font-weight: bold;">{{ $item->item_name }}
                                @if(!empty($item->description) && $item->description !== '-')
                                    <br><span style="font-weight: normal; font-size: 0.68rem; color: #000;">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ $item->item_code }}</td>
                            <td style="text-align: center;">{{ $item->qty_send ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item->total_send ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item->qty_po ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item->unit->name ?? '-' }}</td>
                            {{-- Keterangan di-rowspan: 1 item utama + 1 group title + jumlah sub item --}}
                            <td style="font-size: 0.72rem; vertical-align: middle; text-align: center; padding: 0.5rem 0.25rem;" rowspan="{{ 1 + 1 + $item->subItems->count() }}">{{ $item->information ?? '-' }}</td>
                        </tr>

                        {{-- GROUP TITLE (setiap kolom tetap ada, isi hanya di kolom nama) --}}
                        <tr class="group-row">
                            <td style="text-align: center;"></td>
                            <td>
                                @if(!empty(trim((string)($item->sub_item_group_title ?? ''))))
                                    {{ $item->sub_item_group_title }}
                                @else
                                    Rincian Sub Item
                                @endif
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        {{-- ROW SUB ITEM (kolom No kosong, tanpa keterangan) --}}
                        @foreach($item->subItems as $sIndex => $sub)
                            <tr class="sub-row">
                                <td style="text-align: center;"></td>
                                <td style="text-align: left; padding-left: 12px;">
                                    {{ $sub->item_name }}
                                    @if(!empty($sub->description) && $sub->description !== '-')
                                        <br><span style="font-size: 0.68rem; color: #000;">{{ $sub->description }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">{{ $sub->item_code }}</td>
                                <td style="text-align: center;">{{ $sub->qty_send ?? '-' }}</td>
                                <td style="text-align: center;">{{ $sub->total_send ?? '-' }}</td>
                                <td style="text-align: center;">{{ $sub->qty_po ?? '-' }}</td>
                                <td style="text-align: center;">{{ $sub->unit->name ?? '-' }}</td>
                                {{-- Tidak ada kolom keterangan, sudah di-rowspan dari item utama --}}
                            </tr>
                        @endforeach

                    @else
                        {{-- ROW ITEM UTAMA tanpa sub item, normal --}}
                        <tr>
                            <td style="text-align: center;">
                                <span class="no-badge">{{ $item->no ?? ($index + 1) }}</span>
                            </td>
                            <td style="text-align: left; font-weight: bold;">{{ $item->item_name }}
                                @if(!empty($item->description) && $item->description !== '-')
                                    <br><span style="font-weight: normal; font-size: 0.68rem; color: #000;">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ $item->item_code }}</td>
                            <td style="text-align: center;">{{ $item->qty_send ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item->total_send ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item->qty_po ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item->unit->name ?? '-' }}</td>
                            <td style="font-size: 0.72rem; text-align: center;">{{ $item->information ?? '-' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>


        <!-- Tabel Info Tambahan: Lampiran | Driver & Nopol -->
        {{-- Menggunakan nested table karena DomPDF tidak support rowspan dengan baik --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: 0.75rem; border: 2px solid #000;">
            <tbody>
                <tr>
                    {{-- Kolom kiri: LAMPIRAN --}}
                    <td style="width: 50%; vertical-align: top; padding: 0; border-right: 2px solid #000;">
                        <div style="font-size: 0.72rem; font-weight: bold; background-color: #E5E7EB; padding: 0.35rem 0.5rem; border-bottom: 2px solid #000;">LAMPIRAN</div>
                        <div style="padding: 0.5rem; font-size: 0.75rem; min-height: 80px; vertical-align: top;">
                            @if(isset($travelDocument->attachments) && $travelDocument->attachments->isNotEmpty())
                                @foreach($travelDocument->attachments as $i => $attachment)
                                    {{ $i + 1 }}. {{ $attachment->name }}<br>
                                @endforeach
                            @endif
                        </div>
                    </td>
                    {{-- Kolom kanan: nested table untuk Driver & Nopol --}}
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 0; border-bottom: 2px solid #000; vertical-align: top;">
                                    <div style="font-size: 0.72rem; font-weight: bold; background-color: #E5E7EB; padding: 0.35rem 0.5rem; border-bottom: 2px solid #000;">DRIVER</div>
                                    <div style="padding: 0.5rem; font-size: 0.75rem; min-height: 35px;">
                                        {{ $travelDocument->driver_name ?? '' }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0; vertical-align: top;">
                                    <div style="font-size: 0.72rem; font-weight: bold; background-color: #E5E7EB; padding: 0.35rem 0.5rem; border-bottom: 2px solid #000;">NOPOL</div>
                                    <div style="padding: 0.5rem; font-size: 0.75rem; min-height: 35px;">
                                        {{ $travelDocument->vehicle_number ?? '' }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Section Tanda Tangan -->
        <div style="margin-top: 4rem;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; vertical-align: top; text-align: center; padding: 0.5rem;">
                        <p style="font-size: 0.875rem; font-weight: bold; margin: 0 0 3rem 0;">Diterima Oleh</p>
                        <div style="height: 90px;"></div>
                        <div style="border-top: 1px dotted #000; width: 60%; margin: 0 auto;"></div>
                    </td>
                    <td style="width: 50%; vertical-align: top; text-align: center; padding: 0.5rem;">
                        <p style="font-size: 0.875rem; font-weight: bold; margin: 0;">Yang Menyerahkan</p>
                        <p style="font-size: 0.75rem; margin: 0.25rem 0 2.5rem 0;">PT. REKAINDO GLOBAL JASA</p>
                        <div style="height: 80px;"></div>
                        <div style="border-top: 1px dotted #000; width: 60%; margin: 0 auto;"></div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</body>

</html>
