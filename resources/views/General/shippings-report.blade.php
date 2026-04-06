@extends('layouts.app')

@section('title', 'Bukti Pengiriman - RekaTrack')
@php($pageName = 'Bukti Pengiriman ' . ($travelDocument->no_travel_document ?? ''))

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
    --shadow-lg:    0 8px 24px rgba(0,0,0,.10);
    --transition:   .18s ease;
  }

  .report-wrapper {
    max-width: max-content;
    margin: 0 auto;
    padding-bottom: 3rem;
  }

  /* ===== PAGE HERO ===== */
  .page-hero {
    background: linear-gradient(135deg, #14532d 0%, #16a34a 60%, #22c55e 100%);
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
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
  .hero-left  { position: relative; }
  .hero-label { font-size: .78rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; opacity: .75; margin-bottom: .3rem; }
  .hero-title { font-size: 1.5rem; font-weight: 800; margin: 0 0 .35rem; line-height: 1.2; }
  .hero-status {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(255,255,255,.2);
    border: 1.5px solid rgba(255,255,255,.35);
    border-radius: 99px;
    padding: .3rem .9rem;
    font-size: .82rem;
    font-weight: 700;
  }
  .hero-actions { position: relative; display: flex; gap: .6rem; flex-wrap: wrap; }

  /* ===== SECTION CARD ===== */
  .detail-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    height: 100%;
  }
  .detail-card-header {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
  }
  .detail-card-icon {
    width: 34px; height: 34px;
    background: var(--brand-light);
    color: var(--brand);
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
  }
  .detail-card-icon.green  { background: var(--success-light); color: var(--success); }
  .detail-card-icon.amber  { background: var(--warning-light); color: var(--warning); }
  .detail-card-icon.cyan   { background: var(--info-light);    color: var(--info); }
  .detail-card-title { font-size: .9375rem; font-weight: 700; color: var(--gray-800); margin: 0; }
  .detail-card-body  { padding: 1.25rem 1.5rem; }

  /* ===== INFO TILES ===== */
  .info-grid {
    display: flex;
    flex-direction: column;
    gap: 0;
  }
  .info-row {
    display: flex;
    align-items: flex-start;
    gap: .875rem;
    padding: .875rem 0;
    border-bottom: 1px solid var(--gray-100);
  }
  .info-row:last-child { border-bottom: none; }
  .info-tile-icon {
    width: 38px; height: 38px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
    flex-shrink: 0;
  }
  .info-tile-icon.blue   { background: var(--brand-light);   color: var(--brand);   }
  .info-tile-icon.green  { background: var(--success-light); color: var(--success); }
  .info-tile-icon.amber  { background: var(--warning-light); color: var(--warning); }
  .info-tile-icon.cyan   { background: var(--info-light);    color: var(--info);    }
  .info-tile-icon.red    { background: var(--danger-light);  color: var(--danger);  }
  .info-tile-label { font-size: .72rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--gray-400); margin-bottom: .2rem; }
  .info-tile-value { font-size: .9375rem; font-weight: 700; color: var(--gray-800); margin: 0; line-height: 1.3; }
  .info-tile-value.muted { color: var(--gray-400); font-weight: 400; font-style: italic; }

  /* ===== PHOTO GRID ===== */
  .photo-section {
    background: var(--gray-50);
    border: 1px dashed var(--gray-300);
    border-radius: var(--radius);
    padding: 1.25rem;
    min-height: 180px;
  }
  .photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: .75rem; }
  .photo-item {
    position: relative;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: transform var(--transition), box-shadow var(--transition);
    aspect-ratio: 1 / 1;
  }
  .photo-item:hover { transform: scale(1.03); box-shadow: var(--shadow); }
  .photo-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .photo-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,0);
    display: flex; align-items: center; justify-content: center;
    transition: background var(--transition);
  }
  .photo-item:hover .photo-overlay { background: rgba(0,0,0,.25); }
  .photo-overlay i { color: #fff; font-size: 1.4rem; opacity: 0; transition: opacity var(--transition); }
  .photo-item:hover .photo-overlay i { opacity: 1; }

  /* ===== NOTES ===== */
  .note-section {
    background: var(--warning-light);
    border: 1px solid #fcd34d;
    border-radius: var(--radius);
    padding: 1rem 1.25rem;
    min-height: 180px;
    display: flex;
    gap: .75rem;
  }
  .note-icon { font-size: 1.1rem; color: var(--warning); flex-shrink: 0; margin-top: .1rem; }
  .note-text  { font-size: .9rem; color: var(--gray-700); line-height: 1.6; margin: 0; }

  /* ===== EMPTY STATES ===== */
  .empty-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 180px;
    background: var(--gray-50);
    border: 1px dashed var(--gray-300);
    border-radius: var(--radius);
    color: var(--gray-400);
    gap: .5rem;
  }
  .empty-box i { font-size: 2rem; }
  .empty-box p { margin: 0; font-size: .875rem; }

  /* ===== BOTTOM BAR ===== */
  .bottom-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    padding-top: 1.25rem;
    margin-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
  }

  /* ===== BUTTONS ===== */
  .btn { border-radius: var(--radius-sm); font-size: .875rem; font-weight: 500; transition: all var(--transition); }
  .btn-back { background: var(--gray-100); color: var(--gray-700); border: 1.5px solid var(--gray-200); }
  .btn-back:hover { background: var(--gray-200); color: var(--gray-800); }
  .btn-print { background: var(--brand); color: #fff; border: none; }
  .btn-print:hover { background: var(--brand-dark); color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,.3); }
  .btn-list { background: var(--gray-700); color: #fff; border: none; }
  .btn-list:hover { background: var(--gray-800); color: #fff; }

  /* ===== PRINT STYLES ===== */
  @media print {
    .bottom-bar, .page-hero .hero-actions { display: none !important; }
    .detail-card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
    .page-hero {
      background: #16a34a !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
  }

  @media (max-width: 768px) {
    .page-hero { padding: 1.25rem; }
    .photo-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); }
  }
</style>
@endpush

@section('content')
<div class="report-wrapper">

  {{-- ===== PAGE HERO ===== --}}
  <div class="page-hero">
    <div class="hero-left">
      <div class="hero-label"><i class="fas fa-file-invoice me-1"></i> Bukti Pengiriman</div>
      <h2 class="hero-title">{{ $travelDocument->no_travel_document ?? '-' }}</h2>
      <span class="hero-status">
        <i class="fas fa-check-circle"></i>
        {{ $travelDocument->status ?? 'Terkirim' }}
      </span>
    </div>
    <div class="hero-actions">
      <button onclick="window.print()" class="btn" style="background:rgba(255,255,255,.2);color:#fff;border:1.5px solid rgba(255,255,255,.4)">
        <i class="fas fa-print me-1"></i> Cetak
      </button>
    </div>
  </div>

  {{-- ===== TOP ROW: Informasi Dokumen + Penerimaan ===== --}}
  <div class="row g-3 mb-3">

    {{-- Informasi Dokumen --}}
    <div class="col-lg-6">
      <div class="detail-card">
        <div class="detail-card-header">
          <div class="detail-card-icon"><i class="fas fa-file-alt"></i></div>
          <h5 class="detail-card-title">Informasi Dokumen</h5>
        </div>
        <div class="detail-card-body">
          <div class="info-grid">

            <div class="info-row">
              <div class="info-tile-icon blue"><i class="fas fa-file-invoice"></i></div>
              <div>
                <div class="info-tile-label">No Surat Jalan</div>
                <div class="info-tile-value">{{ $travelDocument->no_travel_document ?? '-' }}</div>
              </div>
            </div>

            <div class="info-row">
              <div class="info-tile-icon amber"><i class="fas fa-truck"></i></div>
              <div>
                <div class="info-tile-label">Driver</div>
                <div class="info-tile-value">
                  @if ($travelDocument->driver)
                    {{ $travelDocument->driver->name }}
                    <span style="font-weight:400;color:var(--gray-500);font-size:.85rem">
                      ({{ $travelDocument->driver->nip }})
                    </span>
                  @else
                    <span class="muted">-</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="info-row">
              <div class="info-tile-icon cyan"><i class="fas fa-calendar-alt"></i></div>
              <div>
                <div class="info-tile-label">Tanggal SJN</div>
                <div class="info-tile-value">
                  @if ($travelDocument->posting_date)
                    {{ \Carbon\Carbon::parse($travelDocument->posting_date)->locale('id')->translatedFormat('d F Y') }}
                  @else
                    <span class="muted">-</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="info-row">
              <div class="info-tile-icon red"><i class="fas fa-map-marker-alt"></i></div>
              <div>
                <div class="info-tile-label">Tujuan</div>
                <div class="info-tile-value">{{ $travelDocument->send_to ?? '-' }}</div>
              </div>
            </div>

            <div class="info-row">
              <div class="info-tile-icon green"><i class="fas fa-project-diagram"></i></div>
              <div>
                <div class="info-tile-label">Proyek</div>
                <div class="info-tile-value">{{ $travelDocument->project ?? '-' }}</div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- Informasi Penerimaan --}}
    <div class="col-lg-6">
      <div class="detail-card">
        <div class="detail-card-header">
          <div class="detail-card-icon green"><i class="fas fa-user-check"></i></div>
          <h5 class="detail-card-title">Informasi Penerimaan</h5>
        </div>
        <div class="detail-card-body">
          <div class="info-grid">

            <div class="info-row">
              <div class="info-tile-icon blue"><i class="fas fa-user"></i></div>
              <div>
                <div class="info-tile-label">Nama Penerima</div>
                <div class="info-tile-value">{{ $confirmation->receiver_name ?? '-' }}</div>
              </div>
            </div>

            <div class="info-row">
              <div class="info-tile-icon green"><i class="fas fa-clock"></i></div>
              <div>
                <div class="info-tile-label">Waktu Diterima</div>
                <div class="info-tile-value">
                  @if(!empty($confirmation->received_at))
                    {{ \Carbon\Carbon::parse($confirmation->received_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                  @else
                    <span class="muted">-</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="info-row">
              <div class="info-tile-icon cyan"><i class="fas fa-play-circle"></i></div>
              <div>
                <div class="info-tile-label">Waktu Mulai Pengiriman</div>
                <div class="info-tile-value">
                  @if(!empty($startTime))
                    {{ $startTime }}
                  @else
                    <span class="muted">-</span>
                  @endif
                </div>
              </div>
            </div>

            @if($travelDocument->start_time && $travelDocument->end_time)
              <div class="info-row">
                <div class="info-tile-icon amber"><i class="fas fa-stopwatch"></i></div>
                <div>
                  <div class="info-tile-label">Durasi Pengiriman</div>
                  <div class="info-tile-value" style="color:var(--warning)">
                    {{ \Carbon\Carbon::parse($travelDocument->start_time)->diffForHumans(\Carbon\Carbon::parse($travelDocument->end_time), true) }}
                  </div>
                </div>
              </div>
            @endif

          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ===== BUKTI FOTO + CATATAN ===== --}}
  <div class="row g-3 mb-0">

    {{-- Bukti Foto --}}
    <div class="col-lg-7">
      <div class="detail-card">
        <div class="detail-card-header">
          <div class="detail-card-icon amber"><i class="fas fa-camera"></i></div>
          <h5 class="detail-card-title">Bukti Foto Pengiriman</h5>
          @if($confirmation && $confirmation->photos->count())
            <span style="margin-left:auto;background:var(--brand-light);color:var(--brand);font-size:.75rem;font-weight:700;padding:.25rem .75rem;border-radius:99px;">
              {{ $confirmation->photos->count() }} foto
            </span>
          @endif
        </div>
        <div class="detail-card-body">
          @if($confirmation && $confirmation->photos->count())
            <div class="photo-grid">
              @foreach($confirmation->photos as $photo)
                <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" class="photo-item">
                  <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Bukti Pengiriman">
                  <div class="photo-overlay"><i class="fas fa-expand-alt"></i></div>
                </a>
              @endforeach
            </div>
          @else
            <div class="empty-box">
              <i class="fas fa-image"></i>
              <p>Tidak ada foto bukti pengiriman</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Catatan --}}
    <div class="col-lg-5">
      <div class="detail-card">
        <div class="detail-card-header">
          <div class="detail-card-icon amber"><i class="fas fa-sticky-note"></i></div>
          <h5 class="detail-card-title">Catatan Penerima</h5>
        </div>
        <div class="detail-card-body">
          @if(!empty($confirmation->note))
            <div class="note-section">
              <i class="fas fa-quote-left note-icon"></i>
              <p class="note-text">{{ $confirmation->note }}</p>
            </div>
          @else
            <div class="empty-box">
              <i class="fas fa-comment-slash"></i>
              <p>Tidak ada catatan dari penerima</p>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>

  {{-- ===== BOTTOM BAR ===== --}}
  <div class="bottom-bar">
    <div>
      <a href="{{ route('shippings.detail', $travelDocument->id) }}" class="btn btn-back">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail
      </a>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('shippings.print-report', $travelDocument->id) }}"
        class="btn btn-print"
        target="_blank">
            <i class="fas fa-print me-1"></i> Cetak Bukti Pengiriman
        </a>
      <a href="{{ route('shippings.index') }}" class="btn btn-list">
        <i class="fas fa-list me-1"></i> Daftar Pengiriman
      </a>
    </div>
  </div>

</div>
@endsection
