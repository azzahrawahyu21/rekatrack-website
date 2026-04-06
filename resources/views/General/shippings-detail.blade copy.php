@extends('layouts.app')

@section('title', 'Detail Pengiriman - RekaTrack')
@php($pageName = 'Detail Pengiriman ' . ($travelDocument->no_travel_document ?? ''))

@push('styles')
<style>
  :root {
    --brand:        #2563eb;
    --brand-light:  #dbeafe;
    --brand-dark:   #1e40af;
    --success:      #16a34a;
    --success-light:#dcfce7;
    --warning:      #d97706;
    --warning-light:#fef3c7;
    --info:         #0891b2;
    --info-light:   #cffafe;
    --danger:       #dc2626;
    --danger-light: #fee2e2;
    --gray-50:      #f8fafc;
    --gray-100:     #f1f5f9;
    --gray-200:     #e2e8f0;
    --gray-300:     #cbd5e1;
    --gray-400:     #94a3b8;
    --gray-500:     #64748b;
    --gray-600:     #475569;
    --gray-700:     #334155;
    --gray-800:     #1e293b;
    --radius-sm:    6px;
    --radius:       10px;
    --radius-lg:    14px;
    --shadow-sm:    0 1px 3px rgba(0,0,0,.07);
    --shadow:       0 4px 12px rgba(0,0,0,.08);
    --transition:   .18s ease;
  }

  .detail-wrapper { max-width: none; margin: 0 auto; padding-bottom: 3rem; }

  .page-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
    border-radius: var(--radius-lg);
    padding: 1.75rem 2rem;
    margin-bottom: 1.75rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
  }
  .page-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
  .hero-left    { position: relative; }
  .hero-sjn     { font-size:.8rem; font-weight:600; letter-spacing:.1em; text-transform:uppercase; opacity:.7; margin-bottom:.35rem; }
  .hero-title   { font-size:1.5rem; font-weight:800; margin:0 0 .4rem; line-height:1.2; }
  .hero-actions { position:relative; display:flex; gap:.6rem; flex-wrap:wrap; }

  .status-pill { display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .9rem; border-radius:99px; font-size:.8rem; font-weight:700; letter-spacing:.02em; }
  .status-pill.belum    { background:var(--warning-light); color:var(--warning);  border:1.5px solid #fcd34d; }
  .status-pill.dikirim  { background:var(--info-light);    color:var(--info);     border:1.5px solid #67e8f9; }
  .status-pill.terkirim { background:var(--success-light); color:var(--success);  border:1.5px solid #86efac; }

  .detail-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); overflow:hidden; height:100%; }
  .detail-card-header { display:flex; align-items:center; gap:.75rem; padding:1rem 1.5rem; border-bottom:1px solid var(--gray-200); background:var(--gray-50); }
  .detail-card-icon { width:34px; height:34px; background:var(--brand-light); color:var(--brand); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:.85rem; flex-shrink:0; }
  .detail-card-title { font-size:.9375rem; font-weight:700; color:var(--gray-800); margin:0; }
  .detail-card-body  { padding:1.25rem 1.5rem; }

  .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
  .info-tile { background:var(--gray-50); border:1px solid var(--gray-200); border-radius:var(--radius); padding:.9rem 1rem; display:flex; align-items:flex-start; gap:.75rem; transition:box-shadow var(--transition); }
  .info-tile:hover { box-shadow:var(--shadow); }
  .info-tile.full { grid-column:span 2; }
  .info-tile-icon { width:36px; height:36px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }
  .info-tile-icon.blue  { background:var(--brand-light);   color:var(--brand);   }
  .info-tile-icon.green { background:var(--success-light); color:var(--success); }
  .info-tile-icon.amber { background:var(--warning-light); color:var(--warning); }
  .info-tile-icon.cyan  { background:var(--info-light);    color:var(--info);    }
  .info-tile-label { font-size:.72rem; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--gray-400); margin-bottom:.25rem; }
  .info-tile-value { font-size:.9375rem; font-weight:700; color:var(--gray-800); line-height:1.3; }
  .info-tile-value.muted { color:var(--gray-400); font-weight:400; font-style:italic; }

  .duration-banner { margin-top:.75rem; background:linear-gradient(90deg,#dbeafe,#eff6ff); border:1px solid #bfdbfe; border-radius:var(--radius); padding:.75rem 1rem; display:flex; align-items:center; gap:.6rem; font-size:.875rem; font-weight:600; color:var(--brand-dark); }

  .ref-row { display:flex; justify-content:space-between; align-items:flex-start; gap:.5rem; padding:.6rem 0; border-bottom:1px solid var(--gray-100); font-size:.875rem; }
  .ref-row:last-child { border-bottom:none; }
  .ref-label { color:var(--gray-500); white-space:nowrap; flex-shrink:0; }
  .ref-value { font-weight:600; color:var(--gray-800); text-align:right; }

  .attachment-chip { display:inline-flex; align-items:center; gap:.35rem; background:var(--brand-light); color:var(--brand-dark); border:1px solid #bfdbfe; border-radius:99px; padding:.25rem .75rem; font-size:.8rem; font-weight:500; }
  .backdate-badge  { display:inline-flex; align-items:center; gap:.3rem; background:var(--warning-light); color:var(--warning); border:1px solid #fcd34d; border-radius:99px; padding:.2rem .65rem; font-size:.72rem; font-weight:700; margin-top:.3rem; }

  .items-table-wrapper { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); overflow:hidden; margin-top:1.5rem; }
  .items-table-header  { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; padding:1rem 1.5rem; border-bottom:1px solid var(--gray-200); background:var(--gray-50); }
  .items-table-header-left { display:flex; align-items:center; gap:.75rem; }
  .count-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .85rem; border-radius:99px; font-size:.78rem; font-weight:700; }
  .count-chip.blue { background:var(--brand-light); color:var(--brand); }
  .count-chip.cyan { background:var(--info-light);  color:var(--info);  }

  .items-table { width:100%; border-collapse:separate; border-spacing:0; }
  .items-table thead tr th { background:linear-gradient(135deg,#1e40af 0%,#2563eb 100%); color:#fff; font-size:.78rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; padding:.875rem 1rem; border:none; white-space:nowrap; }
  .items-table tbody tr.parent-row td { background:#fff; padding:.875rem 1rem; border-bottom:1px solid var(--gray-100); vertical-align:middle; }
  .items-table tbody tr.parent-row:hover td { background:var(--gray-50); }
  .items-table tbody tr.sub-row td { background:#f8faff; padding:.65rem 1rem; border-bottom:1px solid #eef2ff; vertical-align:middle; }
  .items-table tbody tr.sub-row:hover td { background:#f0f4ff; }
  .items-table tbody tr.sub-group-end td { border-bottom:2px solid var(--gray-200); }
  .items-table tbody tr.subgroup-row td { background:#eef2ff; border-bottom:1px solid #dbeafe; padding:.6rem 1rem; }

  .item-no { width:2.5rem; height:2.5rem; background:var(--brand); color:#fff; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:800; flex-shrink:0; }
  .item-name { font-weight:700; color:var(--gray-800); }
  .item-desc { font-size:.8rem; color:var(--gray-400); margin-top:.15rem; }
  .sub-no   { font-size:.72rem; font-weight:700; color:var(--brand); background:var(--brand-light); padding:.2rem .5rem; border-radius:99px; white-space:nowrap; }
  .sub-pill { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; padding:.2rem .6rem; border-radius:99px; border:1px solid #c7d7f5; background:#eef2ff; color:var(--brand-dark); white-space:nowrap; flex-shrink:0; }
  .subgroup-title { display:flex; align-items:center; gap:.5rem; font-weight:700; color:var(--brand-dark); }
  .subgroup-badge { background:var(--brand-light); color:var(--brand-dark); border:1px solid #bfdbfe; padding:.2rem .65rem; border-radius:99px; font-size:.72rem; font-weight:800; }
  .code-badge { background:var(--gray-100); color:var(--gray-700); border:1px solid var(--gray-200); border-radius:var(--radius-sm); padding:.2rem .6rem; font-size:.78rem; font-family:monospace; font-weight:600; }
  .unit-badge { background:var(--gray-200); color:var(--gray-700); border-radius:var(--radius-sm); padding:.2rem .65rem; font-size:.78rem; font-weight:600; }
  .qty-send  { font-weight:700; color:var(--brand); }
  .qty-po    { font-weight:700; color:var(--info); }
  .qty-total { font-weight:700; color:var(--success); }

  .empty-state { text-align:center; padding:3.5rem 1rem; }
  .empty-state i { font-size:2.5rem; color:var(--gray-300); display:block; margin-bottom:.75rem; }
  .empty-state p { color:var(--gray-400); margin:0; }

  .bottom-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; padding:1.25rem 0 0; margin-top:1.5rem; border-top:1px solid var(--gray-200); }

  .btn { border-radius:var(--radius-sm); font-size:.875rem; font-weight:500; transition:all var(--transition); }
  .btn-back     { background:var(--gray-100); color:var(--gray-700); border:1.5px solid var(--gray-200); }
  .btn-back:hover { background:var(--gray-200); color:var(--gray-800); }
  .btn-edit     { background:#fffbeb; color:#92400e; border:1.5px solid #fcd34d; }
  .btn-edit:hover { background:#fef3c7; color:#78350f; }
  .btn-print    { background:var(--brand); color:#fff; border:none; }
  .btn-print:hover { background:var(--brand-dark); color:#fff; box-shadow:0 4px 12px rgba(37,99,235,.3); }
  .btn-report   { background:var(--success); color:#fff; border:none; }
  .btn-report:hover { background:#15803d; color:#fff; }
  .btn-tracking { background:#ffffff; color:#1e40af; border:2px solid #ffffff; font-weight:600; box-shadow:0 4px 14px rgba(0,0,0,.15); transition:all .2s ease; }
  .btn-tracking:hover { background:#1e40af; color:#ffffff; border-color:#1e40af; transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,.25); }

  @media (max-width:768px) {
    .info-grid { grid-template-columns:1fr; }
    .info-tile.full { grid-column:span 1; }
    .page-hero { padding:1.25rem; }
  }
</style>
@endpush

@section('content')
<div class="detail-wrapper">

  {{-- PAGE HERO --}}
  <div class="page-hero">
    <div class="hero-left">
      <div class="hero-sjn">Surat Jalan</div>
      <h2 class="hero-title">{{ $travelDocument->no_travel_document ?? 'Tanpa Nomor' }}</h2>
      @if ($travelDocument->status == 'Belum terkirim')
        <span class="status-pill belum"><i class="fas fa-clock"></i> Belum Terkirim</span>
      @elseif($travelDocument->status == 'Sedang dikirim')
        <span class="status-pill dikirim"><i class="fas fa-truck"></i> Sedang Dikirim</span>
      @elseif($travelDocument->status == 'Terkirim')
        <span class="status-pill terkirim"><i class="fas fa-check-circle"></i> Terkirim</span>
      @else
        <span class="status-pill belum"><i class="fas fa-question-circle"></i> {{ $travelDocument->status ?? '-' }}</span>
      @endif
    </div>
    <div class="hero-actions">
      <a href="{{ route('tracking.detail', $travelDocument->id) }}" class="btn btn-tracking">
        <i class="fas fa-map-marked-alt me-1"></i> Lihat Tracking
      </a>
    </div>
  </div>

  {{-- TOP ROW --}}
  <div class="row g-3 mb-0">

    <div class="col-lg-8">
      <div class="detail-card">
        <div class="detail-card-header">
          <div class="detail-card-icon"><i class="fas fa-info-circle"></i></div>
          <h5 class="detail-card-title">Informasi Pengiriman</h5>
        </div>
        <div class="detail-card-body">
          <div class="info-grid">

            <div class="info-tile">
              <div class="info-tile-icon blue"><i class="fas fa-user-circle"></i></div>
              <div>
                <div class="info-tile-label">Kepada</div>
                <div class="info-tile-value">{{ $travelDocument->send_to ?? '-' }}</div>
              </div>
            </div>

            <div class="info-tile">
              <div class="info-tile-icon green"><i class="fas fa-project-diagram"></i></div>
              <div>
                <div class="info-tile-label">Proyek</div>
                <div class="info-tile-value">{{ $travelDocument->project ?? '-' }}</div>
              </div>
            </div>

            <div class="info-tile">
              <div class="info-tile-icon cyan"><i class="fas fa-calendar-alt"></i></div>
              <div>
                <div class="info-tile-label">Tanggal Dokumen</div>
                <div class="info-tile-value">
                  @if ($travelDocument->document_date)
                    {{ \Carbon\Carbon::parse($travelDocument->document_date)->format('d M Y') }}
                  @else
                    <span class="muted">-</span>
                  @endif
                </div>
                @if ($travelDocument->is_backdate)
                  <span class="backdate-badge"><i class="fas fa-history"></i> Backdate</span>
                @endif
              </div>
            </div>

            <div class="info-tile">
              <div class="info-tile-icon amber"><i class="fas fa-calendar-check"></i></div>
              <div>
                <div class="info-tile-label">Tanggal Posting</div>
                <div class="info-tile-value">
                  @if ($travelDocument->posting_date)
                    {{ \Carbon\Carbon::parse($travelDocument->posting_date)->format('d M Y') }}
                  @else
                    <span class="muted">-</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="info-tile">
              <div class="info-tile-icon blue"><i class="fas fa-play-circle"></i></div>
              <div>
                <div class="info-tile-label">Waktu Mulai Kirim</div>
                @if ($travelDocument->start_time)
                  <div class="info-tile-value" style="color:var(--brand)">
                    {{ \Carbon\Carbon::parse($travelDocument->start_time)->format('d M Y, H:i') }} WIB
                  </div>
                @else
                  <div class="info-tile-value muted">Belum dimulai</div>
                @endif
              </div>
            </div>

            <div class="info-tile">
              <div class="info-tile-icon green"><i class="fas fa-stop-circle"></i></div>
              <div>
                <div class="info-tile-label">Waktu Selesai Kirim</div>
                @if ($travelDocument->end_time)
                  <div class="info-tile-value" style="color:var(--success)">
                    {{ \Carbon\Carbon::parse($travelDocument->end_time)->format('d M Y, H:i') }} WIB
                  </div>
                @else
                  <div class="info-tile-value muted">Belum selesai</div>
                @endif
              </div>
            </div>

            <div class="info-tile full">
              <div class="info-tile-icon cyan"><i class="fas fa-id-card"></i></div>
              <div class="w-100">
                <div class="info-tile-label">Driver & Kendaraan</div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                  <div class="info-tile-value">{{ $travelDocument->driver_name ?: '-' }}</div>
                  <span style="color:var(--gray-300)">•</span>
                  <div class="d-flex align-items-center gap-2">
                    <span class="sub-pill" style="background:var(--gray-100);border-color:var(--gray-200);color:var(--gray-700)">
                      <i class="fas fa-car-side"></i> Nopol
                    </span>
                    <div class="info-tile-value">{{ $travelDocument->vehicle_number ?: '-' }}</div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          @if ($travelDocument->start_time && $travelDocument->end_time)
            <div class="duration-banner">
              <i class="fas fa-stopwatch"></i>
              Durasi Pengiriman:
              <strong>{{ \Carbon\Carbon::parse($travelDocument->start_time)->diffForHumans(\Carbon\Carbon::parse($travelDocument->end_time), true) }}</strong>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="detail-card">
        <div class="detail-card-header">
          <div class="detail-card-icon"><i class="fas fa-tag"></i></div>
          <h5 class="detail-card-title">Referensi & Detail</h5>
        </div>
        <div class="detail-card-body">
          <div class="ref-row">
            <span class="ref-label">Nomor SJN</span>
            <span class="ref-value">{{ $travelDocument->no_travel_document ?? '-' }}</span>
          </div>
          <div class="ref-row">
            <span class="ref-label">PO Number</span>
            <span class="ref-value">{{ $travelDocument->po_number ?? '-' }}</span>
          </div>
          <div class="ref-row">
            <span class="ref-label">Referensi</span>
            <span class="ref-value">{{ $travelDocument->reference_number ?? '-' }}</span>
          </div>
          <div class="ref-row">
            <span class="ref-label">Reference Date</span>
            <span class="ref-value">
              @if (!empty($travelDocument->reference_date))
                {{ \Carbon\Carbon::parse($travelDocument->reference_date)->format('d M Y') }}
              @else
                -
              @endif
            </span>
          </div>
          <div class="ref-row">
            <span class="ref-label">Driver</span>
            <span class="ref-value">
              @if (isset($travelDocument->driver) && $travelDocument->driver)
                {{ $travelDocument->driver->name }}
                <span style="font-weight:400;color:var(--gray-500)">({{ $travelDocument->driver->nip }})</span>
              @else
                -
              @endif
            </span>
          </div>
          <div class="ref-row">
            <span class="ref-label">Jenis Pengiriman</span>
            <span class="ref-value">{{ $travelDocument->delivery_type ?? ($travelDocument->delivery_tipe ?? '-') }}</span>
          </div>
          <div class="ref-row" style="flex-direction:column;gap:.5rem;">
            <span class="ref-label">Lampiran</span>
            @if($travelDocument->attachments->isNotEmpty())
              <div class="d-flex flex-wrap gap-2">
                @foreach($travelDocument->attachments as $attachment)
                  <span class="attachment-chip"><i class="fas fa-paperclip"></i> {{ $attachment->name }}</span>
                @endforeach
              </div>
            @else
              <span class="ref-value">-</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ITEMS TABLE --}}
  <div class="items-table-wrapper">
    <div class="items-table-header">
      <div class="items-table-header-left">
        <div class="detail-card-icon"><i class="fas fa-boxes"></i></div>
        <h5 class="detail-card-title" style="margin:0">Daftar Barang</h5>
      </div>
      <div class="d-flex gap-2">
        <span class="count-chip blue"><i class="fas fa-cube"></i> {{ $travelDocument->items->count() }} Item</span>
        <span class="count-chip cyan"><i class="fas fa-layer-group"></i> {{ $subCount }} Sub Item</span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="items-table">
        <thead>
          <tr>
            <th style="width:5%;text-align:center">No</th>
            <th>Nama / Sub Item</th>
            <th>Kode</th>
            <th style="text-align:center">Qty Kirim</th>
            <th style="text-align:center">Qty PO</th>
            <th style="text-align:center">Total Kirim</th>
            <th style="text-align:center">Satuan</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($travelDocument->items as $index => $item)



            <tr class="parent-row">
              <td style="text-align:center">
                <div class="item-no" style="margin:0 auto">{{ $index + 1 }}</div>
              </td>
              <td>
                <div class="item-name">{{ $item->item_name }}</div>
                @if(!empty($item->description) && $item->description !== '-')
                  <div class="item-desc">{{ $item->description }}</div>
                @endif
              </td>
              <td><span class="code-badge">{{ $item->item_code }}</span></td>
              <td style="text-align:center"><span class="qty-send">{{ $item->qty_send ?? '-' }}</span></td>
              <td style="text-align:center"><span class="qty-po">{{ $item->qty_po ?? '-' }}</span></td>
              <td style="text-align:center"><span class="qty-total">{{ $item->total_send ?? '-' }}</span></td>
              <td style="text-align:center"><span class="unit-badge">{{ $item->unit->name ?? '-' }}</span></td>
              <td style="font-size:.82rem;color:var(--gray-500)">{{ $item->information ?? '-' }}</td>
            </tr>

            @if(isset($item->subItems) && $item->subItems->isNotEmpty())

              {{-- Sub group title row — hanya muncul jika field sub_item_group_title terisi --}}
              @if(!empty($item->sub_item_group_title))
                <tr class="subgroup-row">
                  <td style="text-align:center">
                    <span class="sub-no">{{ $index + 1 }}.*</span>
                  </td>
                  <td colspan="7">
                    <div class="subgroup-title">
                      <span class="subgroup-badge"><i class="fas fa-layer-group me-1"></i> Grup</span>
                      <span>{{ $item->sub_item_group_title }}</span>
                    </div>
                  </td>
                </tr>
              @endif

              @foreach($item->subItems as $sIndex => $sub)
                <tr class="sub-row @if($loop->last) sub-group-end @endif">
                  <td style="text-align:center">
                    <span class="sub-no">{{ $index + 1 }}.{{ $sIndex + 1 }}</span>
                  </td>
                  <td>
                    <div class="d-flex align-items-start gap-2">
                      <span class="sub-pill"><i class="fas fa-level-down-alt"></i> Sub</span>
                      <div>
                        <div style="font-weight:600;color:var(--gray-800)">{{ $sub->item_name }}</div>
                        @if(!empty($sub->description) && $sub->description !== '-')
                          <div class="item-desc">{{ $sub->description }}</div>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td><span class="code-badge">{{ $sub->item_code }}</span></td>
                  <td style="text-align:center"><span class="qty-send">{{ $sub->qty_send ?? '-' }}</span></td>
                  <td style="text-align:center"><span class="qty-po">{{ $sub->qty_po ?? '-' }}</span></td>
                  <td style="text-align:center"><span class="qty-total">{{ $sub->total_send ?? '-' }}</span></td>
                  <td style="text-align:center"><span class="unit-badge">{{ $sub->unit->name ?? '-' }}</span></td>
                  <td style="font-size:.82rem;color:var(--gray-500)">{{ $sub->information ?? '-' }}</td>
                </tr>
              @endforeach

            @endif

          @empty
            <tr>
              <td colspan="8">
                <div class="empty-state">
                  <i class="fas fa-box-open"></i>
                  <p>Tidak ada barang dalam pengiriman ini</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- BOTTOM BAR --}}
  <div class="bottom-bar">
    <div>
      <a href="{{ route('shippings.index') }}" class="btn btn-back">
        <i class="fas fa-arrow-left me-1"></i> Kembali
      </a>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      @if ($travelDocument->status === 'Terkirim')
        <a href="{{ route('shippings.report', $travelDocument->id) }}" class="btn btn-report">
          <i class="fas fa-file-invoice me-1"></i> Bukti Pengiriman
        </a>
      @endif
      <a href="{{ route('shippings.edit', $travelDocument->id) }}" class="btn btn-edit">
        <i class="fas fa-edit me-1"></i> Edit
      </a>
      <form action="{{ route('shippings.print', $travelDocument->id) }}" method="GET" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-print">
          <i class="fas fa-print me-1"></i> Cetak Surat Jalan
        </button>
      </form>
    </div>
  </div>

</div>
@endsection
