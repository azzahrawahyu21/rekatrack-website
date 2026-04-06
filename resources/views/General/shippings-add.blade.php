@extends('layouts.app')

@section('title', 'Tambah Pengiriman - RekaTrack')
@php($pageName = 'Tambah Data Pengiriman')

@push('styles')
    <style>
        /* ===== VARIABLES ===== */
        :root {
            --brand: #2563eb;
            --brand-light: #dbeafe;
            --brand-dark: #1e40af;
            --success: #16a34a;
            --danger: #dc2626;
            --danger-light: #fee2e2;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --radius-sm: 6px;
            --radius: 10px;
            --radius-lg: 14px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .07), 0 1px 2px rgba(0, 0, 0, .04);
            --shadow: 0 4px 12px rgba(0, 0, 0, .08);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, .10);
            --transition: .18s ease;
        }

        /* ===== PAGE LAYOUT ===== */
        .shipping-form-wrapper {
            max-width: max-content;
            margin: 0 auto;
            padding: 0 0 3rem;
        }

        /* ===== PAGE HEADER ===== */
        .page-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            margin-bottom: 2rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .page-hero-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, .15);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: .75rem;
        }

        .page-hero h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 .25rem;
        }

        .page-hero p {
            font-size: .875rem;
            margin: 0;
            opacity: .8;
        }

        /* ===== SECTION CARD ===== */
        .form-section {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .form-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .form-section-header-left {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .section-icon {
            width: 36px;
            height: 36px;
            background: var(--brand-light);
            color: var(--brand);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            flex-shrink: 0;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }

        .section-subtitle {
            font-size: .75rem;
            color: var(--gray-500);
            margin: 0;
        }

        .form-section-body {
            padding: 1.5rem;
        }

        /* ===== FORM CONTROLS ===== */
        .form-label {
            font-size: .8125rem;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: .375rem;
            letter-spacing: .015em;
        }

        .form-control,
        .form-select {
            border-color: var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: .875rem;
            color: var(--gray-800);
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: none;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--danger);
        }

        .form-control.is-invalid:focus,
        .form-select.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .12);
        }

        /* ===== ITEM ROW ===== */
        .item-row {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-bottom: 1rem;
            position: relative;
            transition: box-shadow var(--transition);
        }

        .item-row:hover {
            box-shadow: var(--shadow);
        }

        .item-number-badge {
            position: absolute;
            top: -10px;
            left: 1rem;
            background: var(--brand);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .06em;
            padding: .2rem .6rem;
            border-radius: 99px;
        }

        /* ===== SUB-ITEMS CONTAINER ===== */
        .subitems-wrapper {
            background: #fff;
            border: 1px dashed var(--gray-300);
            border-radius: var(--radius);
            padding: 1rem 1.25rem 1.25rem;
            margin-top: 1rem;
        }

        .subitems-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .75rem;
        }

        .subitems-label {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--gray-500);
        }

        .subitems-label i {
            font-size: .75rem;
        }

        .subitems-container {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        /* ===== SUBITEM ROW ===== */
        .subitem-row {
            background: #f8faff;
            border: 1px solid #c7d7f5;
            border-radius: var(--radius-sm);
            padding: .875rem 1rem;
            position: relative;
            animation: slideInRow .2s ease;
        }

        @keyframes slideInRow {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .subitem-index {
            position: absolute;
            top: 8px;
            left: 10px;
            font-size: .65rem;
            font-weight: 700;
            color: var(--brand);
            background: var(--brand-light);
            padding: .1rem .45rem;
            border-radius: 99px;
            line-height: 1.6;
        }

        .subitem-row .subitem-fields {
            padding-left: 0;
        }

        .subitem-row .subitem-main-fields {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1.2fr 80px 80px 90px 42px;
            gap: .5rem;
            align-items: end;
        }

        .subitem-row .subitem-info-field {
            margin-top: .5rem;
        }

        /* ===== ATTACHMENT ROW ===== */
        .attachment-row .input-group .form-control {
            border-right: none;
        }

        .attachment-row .input-group .btn {
            border-top-right-radius: var(--radius-sm) !important;
            border-bottom-right-radius: var(--radius-sm) !important;
        }

        /* ===== BUTTONS ===== */
        .btn {
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-weight: 500;
            transition: all var(--transition);
        }

        .btn-brand {
            background: var(--brand);
            color: #fff;
            border: none;
        }

        .btn-brand:hover {
            background: var(--brand-dark);
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .3);
        }

        .btn-success-solid {
            background: var(--success);
            color: #fff;
            border: none;
            padding: .625rem 1.75rem;
            font-size: .9375rem;
        }

        .btn-success-solid:hover {
            background: #15803d;
            color: #fff;
            box-shadow: 0 4px 14px rgba(22, 163, 74, .3);
        }

        .btn-ghost-danger {
            background: transparent;
            border: 1.5px solid var(--gray-300);
            color: var(--gray-500);
            padding: .3rem .7rem;
        }

        .btn-ghost-danger:hover {
            background: var(--danger-light);
            border-color: var(--danger);
            color: var(--danger);
        }

        .btn-add-subitem {
            background: var(--brand-light);
            color: var(--brand);
            border: 1.5px solid #93c5fd;
            padding: .3rem .8rem;
            font-size: .8rem;
        }

        .btn-add-subitem:hover {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        /* ===== ITEM FOOTER ===== */
        .item-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: .75rem;
            margin-top: .5rem;
            border-top: 1px solid var(--gray-200);
        }

        /* ===== BOTTOM BAR ===== */
        .form-bottom-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.25rem;
            margin-top: .5rem;
            border-top: 1px solid var(--gray-200);
        }

        /* ===== EMPTY STATE ===== */
        .subitems-empty {
            text-align: center;
            padding: .75rem;
            color: var(--gray-400);
            font-size: .8125rem;
        }

        .subitems-empty i {
            font-size: 1.2rem;
            display: block;
            margin-bottom: .25rem;
        }

        /* ===== TOTAL BADGE ===== */
        .total-badge {
            background: var(--brand-light);
            color: var(--brand);
            font-size: .75rem;
            font-weight: 700;
            padding: .25rem .75rem;
            border-radius: 99px;
        }

        /* ===== REQUIRED STAR ===== */
        .req {
            color: var(--danger);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .subitem-row .subitem-main-fields {
                grid-template-columns: 1fr 1fr;
            }

            .page-hero {
                padding: 1.25rem;
            }
        }

        /* ── Group label (Identitas / Kuantitas) ── */
        .item-group-label {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--gray-400);
            border-bottom: 1px solid var(--gray-200);
            padding-bottom: .375rem;
            margin-bottom: .5rem;
        }

        /* ── Sub item row: hapus padding kiri absolut, pakai grid biasa ── */
        .subitem-row .subitem-fields {
            padding-left: 0;
            margin-top: .5rem;
        }

        /* ── Sub item index badge geser agar tidak tumpuk ── */
        .subitem-row {
            padding-top: 2rem;
            /* beri ruang untuk badge */
        }

        .subitem-index {
            top: .5rem;
            left: .75rem;
        }
    </style>
@endpush

@section('content')
    <div class="shipping-form-wrapper">
        <form method="POST" action="{{ route('shippings.store') }}" id="shippingForm">
            @csrf

            {{-- ===== PAGE HERO ===== --}}
            <div class="page-hero">
                <div class="page-hero-icon"><i class="fas fa-truck"></i></div>
                <h2>Form Pengiriman Baru</h2>
                <p>Lengkapi seluruh data di bawah sebelum menyimpan dokumen pengiriman.</p>
            </div>

            {{-- ===== SECTION 1: IDENTITAS DOKUMEN ===== --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="form-section-header-left">
                        <div class="section-icon"><i class="fas fa-file-alt"></i></div>
                        <div>
                            <p class="section-title">Identitas Dokumen</p>
                            <p class="section-subtitle">Nomor SJN, tanggal, dan informasi referensi</p>
                        </div>
                    </div>
                </div>
                <div class="form-section-body">
                    <div class="row g-3">
                        {{-- Nomor SJN --}}
                        <div class="col-md-4">
                            <label class="form-label">Nomor SJN <span class="req">*</span></label>
                            <input type="text" name="numberSJN" value="{{ old('numberSJN') }}"
                                class="form-control @error('numberSJN') is-invalid @enderror"
                                placeholder="Contoh: SJN/2025/001" required />
                            @error('numberSJN')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Dokumen --}}
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Dokumen</label>
                            <input type="date" name="documentDate" value="{{ old('documentDate', date('Y-m-d')) }}"
                                class="form-control @error('documentDate') is-invalid @enderror" />
                            <small class="text-muted">Kosongkan untuk pakai tanggal hari ini.</small>
                            @error('documentDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor Ref --}}
                        <div class="col-md-4">
                            <label class="form-label">Nomor Ref <span class="req">*</span></label>
                            <input type="text" name="numberRef" value="{{ old('numberRef') }}"
                                class="form-control @error('numberRef') is-invalid @enderror" placeholder="Nomor referensi"
                                required />
                            @error('numberRef')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Ref --}}
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Ref</label>
                            <input type="date" name="referenceDate" value="{{ old('referenceDate') }}"
                                class="form-control @error('referenceDate') is-invalid @enderror" />
                            @error('referenceDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kepada --}}
                        <div class="col-md-4">
                            <label class="form-label">Kepada <span class="req">*</span></label>
                            <input type="text" name="sendTo" value="{{ old('sendTo') }}"
                                class="form-control @error('sendTo') is-invalid @enderror" placeholder="Nama penerima"
                                required />
                            @error('sendTo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor PO --}}
                        <div class="col-md-4">
                            <label class="form-label">Nomor PO <span class="req">*</span></label>
                            <input type="text" name="poNumber" value="{{ old('poNumber') }}"
                                class="form-control @error('poNumber') is-invalid @enderror"
                                placeholder="Nomor purchase order" required />
                            @error('poNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Proyek --}}
                        <div class="col-md-6">
                            <label class="form-label">Proyek <span class="req">*</span></label>
                            <input type="text" name="projectName" value="{{ old('projectName') }}"
                                class="form-control @error('projectName') is-invalid @enderror" placeholder="Nama proyek"
                                required />
                            @error('projectName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jenis Pengiriman --}}
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pengiriman <span class="req">*</span></label>
                            <select name="deliveryType" class="form-select @error('deliveryType') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Dalam Kota"
                                    {{ old('deliveryType', 'Dalam Kota') == 'Dalam Kota' ? 'selected' : '' }}>Dalam Kota
                                </option>
                                <option value="Luar Kota" {{ old('deliveryType') == 'Luar Kota' ? 'selected' : '' }}>Luar
                                    Kota</option>
                            </select>
                            @error('deliveryType')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== SECTION 2: LAMPIRAN ===== --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="form-section-header-left">
                        <div class="section-icon"><i class="fas fa-paperclip"></i></div>
                        <div>
                            <p class="section-title">Lampiran</p>
                            <p class="section-subtitle">Nama dokumen pendukung (teks saja, tanpa upload)</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-brand btn-sm" id="addAttachmentBtn">
                        <i class="fas fa-plus me-1"></i> Tambah Lampiran
                    </button>
                </div>
                <div class="form-section-body">
                    <div id="attachmentContainer">
                        @if (is_array(old('attachments')) && count(old('attachments')))
                            @foreach (old('attachments') as $i => $attName)
                                <div class="attachment-row mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent text-muted"
                                            style="font-size:.8rem;border-right:none;">
                                            <i class="fas fa-file-text"></i>
                                        </span>
                                        <input type="text" name="attachments[]" value="{{ $attName }}"
                                            class="form-control @error("attachments.$i") is-invalid @enderror"
                                            placeholder="Contoh: IS Document" />
                                        <button type="button" class="btn btn-ghost-danger remove-attachment">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    @error("attachments.$i")
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        @else
                            <div class="attachment-row mb-2">
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent text-muted"
                                        style="font-size:.8rem;border-right:none;">
                                        <i class="fas fa-file-text"></i>
                                    </span>
                                    <input type="text" name="attachments[]" class="form-control"
                                        placeholder="Contoh: IS Document" />
                                    <button type="button" class="btn btn-ghost-danger remove-attachment">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== SECTION: DRIVER & KENDARAAN ===== --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="form-section-header-left">
                        <div class="section-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div>
                            <p class="section-title">Driver & Kendaraan</p>
                            <p class="section-subtitle">Informasi pengemudi dan nomor polisi kendaraan</p>
                        </div>
                    </div>
                </div>

                <div class="form-section-body">
                    <div class="row g-3">

                        {{-- Nama Driver --}}
                        <div class="col-md-6">
                            <label class="form-label">Nama Driver</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent text-muted"
                                    style="font-size:.8rem;border-right:none;">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="driverName"
                                    value="{{ old('driverName', $travelDocument->driver_name ?? '') }}"
                                    class="form-control @error('driverName') is-invalid @enderror"
                                    placeholder="Contoh: Nama Driver" />
                            </div>
                            @error('driverName')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nopol Kendaraan --}}
                        <div class="col-md-6">
                            <label class="form-label">No Polisi (Nopol)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent text-muted"
                                    style="font-size:.8rem;border-right:none;">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" name="vehicleNumber"
                                    value="{{ old('vehicleNumber', $travelDocument->vehicle_number ?? '') }}"
                                    class="form-control @error('vehicleNumber') is-invalid @enderror"
                                    placeholder="Contoh: Nomor Polisi Kendaraan" />
                            </div>
                            @error('vehicleNumber')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <small class="text-muted mt-2 d-block">
                        Kosongkan jika tidak menggunakan driver internal.
                    </small>
                </div>
            </div>

            {{-- ===== SECTION 3: DATA BARANG ===== --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="form-section-header-left">
                        <div class="section-icon"><i class="fas fa-boxes"></i></div>
                        <div>
                            <p class="section-title">Data Barang</p>
                            <p class="section-subtitle">Item yang dikirimkan beserta sub-itemnya</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="total-badge"><span id="totalBarang">0</span> item</span>
                        <button type="button" class="btn btn-brand btn-sm" id="addItemBtn">
                            <i class="fas fa-plus me-1"></i> Tambah Barang
                        </button>
                    </div>
                </div>
                <div class="form-section-body">
                    <div id="itemsContainer">
                        @foreach ($items as $index => $item)
                            <div class="item-row">
                                <span class="item-number-badge">ITEM #{{ $index + 1 }}</span>

                                <div class="row g-3 mt-1">

                                    {{-- ── KIRI: Identitas Barang ── --}}
                                    <div class="col-md-6">
                                        <div class="item-group-label">Identitas Barang</div>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label">Nama Barang <span
                                                        class="req">*</span></label>
                                                <input type="text" name="itemName[]"
                                                    value="{{ old("itemName.$index", $item['itemName']) }}"
                                                    class="form-control @error("itemName.$index") is-invalid @enderror"
                                                    placeholder="Contoh: Baut M10" required />
                                                @error("itemName.$index")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label">No Barang</label>
                                                <input type="number" name="no[]"
                                                    value="{{ old("no.$index", $item['no'] ?? '') }}"
                                                    class="form-control @error("no.$index") is-invalid @enderror"
                                                    placeholder="Opsional" />
                                                @error("no.$index")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Boleh kosong (custom).</small>
                                            </div>

                                            <div class="col-4">
                                                <label class="form-label">Kode Barang <span
                                                        class="req">*</span></label>
                                                <input type="text" name="itemCode[]"
                                                    value="{{ old("itemCode.$index", $item['itemCode']) }}"
                                                    class="form-control @error("itemCode.$index") is-invalid @enderror"
                                                    placeholder="SKU / Part No" required />
                                                @error("itemCode.$index")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-4">
                                                <label class="form-label">Satuan <span class="req">*</span></label>
                                                <select name="unitType[]"
                                                    class="form-select @error("unitType.$index") is-invalid @enderror"
                                                    required>
                                                    <option value="">-- pilih --</option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}"
                                                            {{ old("unitType.$index", $item['unitType']) == $unit->id ? 'selected' : '' }}>
                                                            {{ $unit->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error("unitType.$index")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Keterangan</label>
                                                <input type="text" name="information[]"
                                                    value="{{ old("information.$index", $item['information']) }}"
                                                    class="form-control @error("information.$index") is-invalid @enderror"
                                                    placeholder="Opsional" />
                                                @error("information.$index")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ── KANAN: Kuantitas ── --}}
                                    <div class="col-md-6">
                                        <div class="item-group-label">Kuantitas</div>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label">Qty Kirim</label>
                                                <input type="text" name="quantitySend[]"
                                                    value="{{ old("quantitySend.$index", $item['quantitySend']) }}"
                                                    class="form-control @error("quantitySend.$index") is-invalid @enderror"
                                                    placeholder="0" />
                                                @error("quantitySend.$index")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label">Qty PO</label>
                                                <input type="text" name="qtyPreOrder[]"
                                                    value="{{ old("qtyPreOrder.$index", $item['qtyPreOrder']) }}"
                                                    class="form-control @error("qtyPreOrder.$index") is-invalid @enderror"
                                                    placeholder="-" />
                                                @error("qtyPreOrder.$index")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label">Total Kirim <span
                                                        class="req">*</span></label>
                                                <input type="text" name="totalSend[]"
                                                    value="{{ old("totalSend.$index", $item['totalSend']) }}"
                                                    class="form-control @error("totalSend.$index") is-invalid @enderror"
                                                    placeholder="0" required />
                                                @error("totalSend.$index")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ── SUB ITEMS ── --}}
                                    <div class="col-12">
                                        <div class="subitems-wrapper">
                                            {{-- 🔥 JUDUL GROUP SUB ITEM --}}
                                            <div class="col-12">
                                                <label class="form-label">Judul Grup Sub Item</label>
                                                <input type="text" name="subItemGroupTitle[]"
                                                    value="{{ old("subItemGroupTitle.$index", $item['subItemGroupTitle'] ?? '') }}"
                                                    class="form-control" placeholder="Contoh: Paket Panel Control" />
                                                <small class="text-muted">
                                                    Opsional. Akan tampil sebagai header sebelum daftar sub item.
                                                </small>
                                            </div>

                                            <div class="subitems-header">
                                                <span class="subitems-label">
                                                    <i class="fas fa-level-down-alt"></i> Sub Item
                                                </span>
                                                <button type="button" class="btn btn-add-subitem btn-sm add-subitem">
                                                    <i class="fas fa-plus me-1"></i> Tambah Sub Item
                                                </button>
                                            </div>

                                            <div class="subitems-container">
                                                @php($oldSubs = old("subItems.$index", []))
                                                @if (is_array($oldSubs) && count($oldSubs))
                                                    @foreach ($oldSubs as $sIndex => $sub)
                                                        <div class="subitem-row">
                                                            <span class="subitem-index">sub {{ $sIndex + 1 }}</span>
                                                            <div class="subitem-fields">

                                                                {{-- Sub item: baris 1 kiri-kanan --}}
                                                                <div class="row g-2">
                                                                    <div class="col-md-6">
                                                                        <div class="row g-2">
                                                                            <div class="col-12">
                                                                                <label class="form-label">Nama Sub
                                                                                    Item</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="subItems[{{ $index }}][{{ $sIndex }}][item_name]"
                                                                                    value="{{ $sub['item_name'] ?? '' }}"
                                                                                    placeholder="Nama sub item">
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <label class="form-label">Kode</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="subItems[{{ $index }}][{{ $sIndex }}][item_code]"
                                                                                    value="{{ $sub['item_code'] ?? '' }}"
                                                                                    placeholder="Kode">
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <label class="form-label">Satuan</label>
                                                                                <select class="form-select form-select-sm"
                                                                                    name="subItems[{{ $index }}][{{ $sIndex }}][unit_id]">
                                                                                    <option value="">-- pilih --
                                                                                    </option>
                                                                                    @foreach ($units as $unit)
                                                                                        <option
                                                                                            value="{{ $unit->id }}"
                                                                                            {{ ($sub['unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>
                                                                                            {{ $unit->name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="row g-2">
                                                                            <div class="col-4">
                                                                                <label class="form-label">Qty Kirim</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="subItems[{{ $index }}][{{ $sIndex }}][qty_send]"
                                                                                    value="{{ $sub['qty_send'] ?? '' }}"
                                                                                    placeholder="0">
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <label class="form-label">Qty PO</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="subItems[{{ $index }}][{{ $sIndex }}][qty_po]"
                                                                                    value="{{ $sub['qty_po'] ?? '' }}"
                                                                                    placeholder="-">
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <label class="form-label">Total
                                                                                    Kirim</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="subItems[{{ $index }}][{{ $sIndex }}][total_send]"
                                                                                    value="{{ $sub['total_send'] ?? '' }}"
                                                                                    placeholder="0">
                                                                            </div>
                                                                            <div class="col-12 d-flex align-items-end"
                                                                                style="padding-top:.25rem;">
                                                                                <button type="button"
                                                                                    class="btn btn-ghost-danger btn-sm remove-subitem">
                                                                                    <i class="fas fa-times me-1"></i> Hapus
                                                                                    Sub Item
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="subitems-empty">
                                                        <i class="fas fa-layer-group"></i>
                                                        Belum ada sub item. Klik tombol di atas untuk menambahkan.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Item Footer --}}
                                    <div class="col-12">
                                        <div class="item-footer">
                                            <button type="button" class="btn btn-ghost-danger btn-sm remove-item"
                                                {{ count($items) <= 1 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash me-1"></i> Hapus Item Ini
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ===== BOTTOM BAR ===== --}}
            <div class="form-bottom-bar">
                <a href="{{ route('shippings.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-success-solid">
                    <i class="fas fa-save me-1"></i> Simpan Pengiriman
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==========================
            // HELPERS
            // ==========================
            const container = document.getElementById('itemsContainer');
            const addItemBtn = document.getElementById('addItemBtn');
            const units = @json($units);

            function getUnitOptions(selected = '') {
                let html = '<option value="">-- pilih --</option>';
                units.forEach(unit => {
                    const sel = String(selected) === String(unit.id) ? 'selected' : '';
                    html += `<option value="${unit.id}" ${sel}>${unit.name}</option>`;
                });
                return html;
            }

            function updateTotalBarang() {
                document.getElementById('totalBarang').textContent =
                    container.querySelectorAll('.item-row').length;
            }

            function getItemIndex(itemRow) {
                return Array.from(container.querySelectorAll('.item-row')).indexOf(itemRow);
            }

            function reindexItems() {
                container.querySelectorAll('.item-row').forEach((row, itemIndex) => {
                    // update badge
                    const badge = row.querySelector('.item-number-badge');
                    if (badge) badge.textContent = `ITEM #${itemIndex + 1}`;

                    row.querySelectorAll('.subitem-row').forEach((subRow, subIndex) => {
                        // update sub index badge
                        const subBadge = subRow.querySelector('.subitem-index');
                        if (subBadge) subBadge.textContent = `sub ${subIndex + 1}`;

                        subRow.querySelectorAll('input[name], select[name], textarea[name]')
                            .forEach(el => {
                                const name = el.getAttribute('name');
                                if (!name) return;
                                el.setAttribute('name',
                                    name.replace(/^subItems\[\d+\]\[\d+\]/,
                                        `subItems[${itemIndex}][${subIndex}]`)
                                );
                            });
                    });
                });
            }

            // ==========================
            // SUB ITEM ROW TEMPLATE
            // ==========================
            // ─── VERSI BARU: createSubItemRow ───
            function createSubItemRow(itemIndex, subIndex) {
                return `
      <div class="subitem-row">
        <span class="subitem-index">sub ${subIndex + 1}</span>
        <div class="subitem-fields">
          <div class="row g-2">
            <div class="col-md-6">
              <div class="row g-2">
                <div class="col-12">
                  <label class="form-label">Nama Sub Item</label>
                  <input type="text" class="form-control form-control-sm"
                    name="subItems[${itemIndex}][${subIndex}][item_name]"
                    placeholder="Nama sub item">
                </div>
                <div class="col-6">
                  <label class="form-label">Kode</label>
                  <input type="text" class="form-control form-control-sm"
                    name="subItems[${itemIndex}][${subIndex}][item_code]"
                    placeholder="Kode">
                </div>
                <div class="col-6">
                  <label class="form-label">Satuan</label>
                  <select class="form-select form-select-sm"
                    name="subItems[${itemIndex}][${subIndex}][unit_id]">
                    ${getUnitOptions()}
                  </select>
                </div>

              </div>
            </div>
            <div class="col-md-6">
              <div class="row g-2">
                <div class="col-4">
                  <label class="form-label">Qty Kirim</label>
                  <input type="text" class="form-control form-control-sm"
                    name="subItems[${itemIndex}][${subIndex}][qty_send]" placeholder="0">
                </div>
                <div class="col-4">
                  <label class="form-label">Qty PO</label>
                  <input type="text" class="form-control form-control-sm"
                    name="subItems[${itemIndex}][${subIndex}][qty_po]" placeholder="-">
                </div>
                <div class="col-4">
                  <label class="form-label">Total Kirim</label>
                  <input type="text" class="form-control form-control-sm"
                    name="subItems[${itemIndex}][${subIndex}][total_send]" placeholder="0">
                </div>
                <div class="col-12" style="padding-top:.25rem;">
                  <button type="button" class="btn btn-ghost-danger btn-sm remove-subitem">
                    <i class="fas fa-times me-1"></i> Hapus Sub Item
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
            }

            // ==========================
            // ITEM ROW TEMPLATE
            // ==========================
            // ─── VERSI BARU: createNewItemRow ───
            function createNewItemRow() {
                return `
      <div class="item-row">
        <span class="item-number-badge">ITEM #—</span>
        <div class="row g-3 mt-1">

          <div class="col-md-6">
            <div class="item-group-label">Identitas Barang</div>
            <div class="row g-2">
              <div class="col-12">
                <label class="form-label">Nama Barang <span class="req">*</span></label>
                <input type="text" name="itemName[]" class="form-control" placeholder="Contoh: Baut M10" required />
              </div>

                <div class="col-4">
                <label class="form-label">No Barang</label>
                <input type="number" name="no[]" class="form-control" placeholder="Opsional" />
                <small class="text-muted">Boleh kosong (custom).</small>
                </div>

              <div class="col-4">
                <label class="form-label">Kode Barang <span class="req">*</span></label>
                <input type="text" name="itemCode[]" class="form-control" placeholder="SKU / Part No" required />
              </div>
              <div class="col-4">
                <label class="form-label">Satuan <span class="req">*</span></label>
                <select name="unitType[]" class="form-select" required>
                  ${getUnitOptions()}
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Keterangan</label>
                <input type="text" name="information[]" class="form-control" placeholder="Opsional" />
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="item-group-label">Kuantitas</div>
            <div class="row g-2">
              <div class="col-4">
                <label class="form-label">Qty Kirim</label>
                <input type="text" name="quantitySend[]" class="form-control" placeholder="0" />
              </div>
              <div class="col-4">
                <label class="form-label">Qty PO</label>
                <input type="text" name="qtyPreOrder[]" class="form-control" placeholder="-" />
              </div>
              <div class="col-4">
                <label class="form-label">Total Kirim <span class="req">*</span></label>
                <input type="text" name="totalSend[]" class="form-control" placeholder="0" required />
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="subitems-wrapper">
              <div class="col-12">
                <label class="form-label">Judul Grup Sub Item</label>
                <input type="text"
                    name="subItemGroupTitle[]"

                    class="form-control"
                    placeholder="Contoh: Paket Panel Control" />
                <small class="text-muted">
                    Opsional. Akan tampil sebagai header sebelum daftar sub item.
                </small>
              </div>
              <div class="subitems-header">
                <span class="subitems-label">
                  <i class="fas fa-level-down-alt"></i> Sub Item
                </span>
                <button type="button" class="btn btn-add-subitem btn-sm add-subitem">
                  <i class="fas fa-plus me-1"></i> Tambah Sub Item
                </button>
              </div>
              <div class="subitems-container">
                <div class="subitems-empty">
                  <i class="fas fa-layer-group"></i>
                  Belum ada sub item. Klik tombol di atas untuk menambahkan.
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="item-footer">
              <button type="button" class="btn btn-ghost-danger btn-sm remove-item">
                <i class="fas fa-trash me-1"></i> Hapus Item Ini
              </button>
            </div>
          </div>

        </div>
      </div>
    `;
            }

            // ==========================
            // EVENT LISTENERS
            // ==========================
            addItemBtn.addEventListener('click', function() {
                if (container.querySelectorAll('.item-row').length >= 50) return;
                container.insertAdjacentHTML('beforeend', createNewItemRow());
                updateTotalBarang();
                reindexItems();
            });

            container.addEventListener('click', function(e) {

                // ---- Remove item ----
                if (e.target.closest('.remove-item')) {
                    const row = e.target.closest('.item-row');
                    if (container.querySelectorAll('.item-row').length > 1) {
                        row.remove();
                        updateTotalBarang();
                        reindexItems();
                    }
                    return;
                }

                // ---- Add sub item ----
                if (e.target.closest('.add-subitem')) {
                    const itemRow = e.target.closest('.item-row');
                    const subContainer = itemRow.querySelector('.subitems-container');
                    const itemIndex = getItemIndex(itemRow);
                    const subIndex = subContainer.querySelectorAll('.subitem-row').length;

                    // Remove empty state placeholder
                    const empty = subContainer.querySelector('.subitems-empty');
                    if (empty) empty.remove();

                    subContainer.insertAdjacentHTML('beforeend', createSubItemRow(itemIndex, subIndex));
                    reindexItems();
                    return;
                }

                // ---- Remove sub item ----
                if (e.target.closest('.remove-subitem')) {
                    const subRow = e.target.closest('.subitem-row');
                    const subContainer = subRow.closest('.subitems-container');
                    subRow.remove();

                    // Show empty state if no subs left
                    if (!subContainer.querySelector('.subitem-row')) {
                        subContainer.insertAdjacentHTML('beforeend', `
          <div class="subitems-empty">
            <i class="fas fa-layer-group"></i>
            Belum ada sub item. Klik tombol di atas untuk menambahkan.
          </div>
        `);
                    }

                    reindexItems();
                    return;
                }
            });

            // Initial render
            updateTotalBarang();
            reindexItems();

            // ==========================
            // ATTACHMENTS
            // ==========================
            const attachmentContainer = document.getElementById('attachmentContainer');
            const addAttachmentBtn = document.getElementById('addAttachmentBtn');

            function createAttachmentRow() {
                return `
      <div class="attachment-row mb-2">
        <div class="input-group">
          <span class="input-group-text bg-transparent text-muted" style="font-size:.8rem;border-right:none;">
            <i class="fas fa-file-text"></i>
          </span>
          <input type="text" name="attachments[]" class="form-control"
            placeholder="Contoh: IS Document" />
          <button type="button" class="btn btn-ghost-danger remove-attachment">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    `;
            }

            addAttachmentBtn.addEventListener('click', function() {
                attachmentContainer.insertAdjacentHTML('beforeend', createAttachmentRow());
            });

            attachmentContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-attachment')) {
                    const row = e.target.closest('.attachment-row');
                    if (attachmentContainer.querySelectorAll('.attachment-row').length > 1) {
                        row.remove();
                    } else {
                        const input = row.querySelector('input[name="attachments[]"]');
                        if (input) input.value = '';
                    }
                }
            });
        });
    </script>
@endpush
