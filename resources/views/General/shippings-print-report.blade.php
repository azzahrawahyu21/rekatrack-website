<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Bukti Pengiriman - {{ $travelDocument->no_travel_document ?? '' }}</title>

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    @page {
      size: A4;
      margin: 0mm;
    }

    body {
      font-family: 'DejaVu Sans', Arial, sans-serif;
      color: #1a202c;
      background: #ffffff;
      font-size: 11px;
      width: 210mm;
    }

    /* ─── TOP ACCENT BAR ─── */
    .accent-bar {
      height: 6px;
      background: #1a56db;
    }

    /* ─── HEADER ─── */
    .header {
      padding: 20px 28px 0 28px;
      display: table;
      width: 100%;
      table-layout: fixed;
    }
    .header-brand {
      display: table-cell;
      width: 38%;
      vertical-align: middle;
    }
    .header-center {
      display: table-cell;
      width: 24%;
      vertical-align: middle;
      text-align: center;
    }
    .header-meta {
      display: table-cell;
      width: 38%;
      vertical-align: middle;
      text-align: right;
    }

    .logo-img {
      width: 80px;
      height: auto;
      display: block;
      margin-bottom: 6px;
    }

    .brand-name {
      font-size: 13px;
      font-weight: 700;
      color: #1a56db;
      letter-spacing: .5px;
    }
    .brand-tagline {
      font-size: 9px;
      color: #718096;
      margin-top: 2px;
    }

    .doc-title-block {
      background: #1a56db;
      border-radius: 8px;
      padding: 10px 8px;
    }
    .doc-title {
      font-size: 13px;
      font-weight: 700;
      color: #fff;
      letter-spacing: .4px;
      text-align: center;
    }
    .doc-subtitle-txt {
      font-size: 8.5px;
      color: rgba(255,255,255,.75);
      text-align: center;
      margin-top: 3px;
    }

    .meta-badge {
      display: inline-block;
      background: #f0f4ff;
      border: 1px solid #c3d2f0;
      border-radius: 6px;
      padding: 8px 12px;
      text-align: right;
    }
    .meta-label {
      font-size: 9px;
      color: #718096;
      text-transform: uppercase;
      letter-spacing: .5px;
    }
    .meta-value {
      font-size: 11.5px;
      font-weight: 700;
      color: #1a202c;
      margin-top: 2px;
    }
    .meta-value-sm {
      font-size: 10px;
      color: #1a56db;
      font-weight: 700;
      margin-top: 4px;
    }

    /* ─── DIVIDER ─── */
    .divider {
      height: 1px;
      background: #e2e8f0;
      margin: 16px 28px;
    }

    /* ─── SECTION WRAPPER ─── */
    .body-wrap {
      padding: 0 28px;
    }

    /* ─── TWO COL GRID ─── */
    .row {
      display: table;
      width: 100%;
      table-layout: fixed;
      border-spacing: 0;
    }
    .col-half {
      display: table-cell;
      width: 50%;
      vertical-align: top;
      padding-right: 8px;
    }
    .col-half:last-child { padding-right: 0; padding-left: 8px; }

    /* ─── CARD ─── */
    .card {
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      overflow: hidden;
      background: #fff;
      page-break-inside: avoid;
    }

    .card-head {
      display: table;
      width: 100%;
      background: #f7f9fc;
      border-bottom: 1px solid #e2e8f0;
      padding: 9px 14px;
    }
    .card-head-left {
      display: table-cell;
      vertical-align: middle;
    }
    .card-head-right {
      display: table-cell;
      text-align: right;
      vertical-align: middle;
    }
    .card-title {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .7px;
      color: #2d3748;
    }
    .card-title-dot {
      display: inline-block;
      width: 6px;
      height: 6px;
      background: #1a56db;
      border-radius: 50%;
      margin-right: 6px;
      vertical-align: middle;
    }
    .card-badge {
      font-size: 9px;
      background: #ebf4ff;
      color: #1a56db;
      border: 1px solid #bee3f8;
      border-radius: 999px;
      padding: 2px 8px;
      font-weight: 700;
    }

    .card-body { padding: 10px 14px; }

    /* ─── INFO ROWS ─── */
    .info-row {
      display: table;
      width: 100%;
      padding: 8px 0;
      border-bottom: 1px solid #f0f4f8;
    }
    .info-row:last-child {
      border-bottom: 0;
      padding-bottom: 0;
    }
    .info-row:first-child { padding-top: 2px; }

    .lbl {
      display: table-cell;
      width: 40%;
      vertical-align: top;
      font-size: 9.5px;
      color: #718096;
      text-transform: uppercase;
      letter-spacing: .4px;
      padding-right: 8px;
      padding-top: 1px;
    }
    .val {
      display: table-cell;
      width: 60%;
      vertical-align: top;
      font-size: 11.5px;
      font-weight: 700;
      color: #1a202c;
      word-break: break-word;
    }
    .val-sub {
      display: block;
      font-size: 10px;
      font-weight: 600;
      color: #718096;
      margin-top: 1px;
    }
    .val-chip {
      display: inline-block;
      font-size: 9px;
      padding: 2px 7px;
      border-radius: 999px;
      background: #fff3cd;
      border: 1px solid #f6c343;
      color: #92400e;
      font-weight: 700;
      margin-left: 6px;
      vertical-align: middle;
    }

    /* ─── SECTION SPACER ─── */
    .spacer { height: 14px; }

    /* ─── PHOTOS ─── */
    .photos-wrap {
      display: table;
      width: 100%;
    }
    .photo-cell {
      display: inline-block;
      width: 31%;
      margin-right: 3%;
      margin-bottom: 8px;
      vertical-align: top;
    }
    .photo-cell:nth-child(3n) { margin-right: 0; }

    .photo-frame {
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
      height: 120px;
      background: #f7f9fc;
    }
    .photo-frame img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      display: block;
    }
    .photo-empty {
      color: #a0aec0;
      font-size: 11px;
      padding: 12px 0;
      text-align: center;
    }

    /* ─── NOTE ─── */
    .note-box {
      min-height: 120px;
      background: #fffbf0;
      border: 1px solid #f6d860;
      border-radius: 8px;
      padding: 12px;
      font-size: 11px;
      line-height: 1.6;
      color: #3d3300;
      word-break: break-word;
    }
    .note-empty { color: #a0aec0; font-style: italic; }

    /* ─── STATUS STRIP ─── */
    .status-strip {
      background: #f0f9f4;
      border: 1px solid #c6f6d5;
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 14px;
      display: table;
      width: 100%;
    }
    .status-icon {
      display: table-cell;
      vertical-align: middle;
      width: 28px;
      font-size: 15px;
      color: #22863a;
    }
    .status-text {
      display: table-cell;
      vertical-align: middle;
    }
    .status-title {
      font-size: 11px;
      font-weight: 700;
      color: #155724;
    }
    .status-desc {
      font-size: 9.5px;
      color: #2f7a4a;
      margin-top: 1px;
    }

    /* ─── FOOTER ─── */
    .footer-divider {
      height: 1px;
      background: #e2e8f0;
      margin: 16px 28px 12px 28px;
    }
    .footer {
      display: table;
      width: 100%;
      padding: 0 28px 18px 28px;
    }
    .footer-left { display: table-cell; vertical-align: middle; }
    .footer-right { display: table-cell; text-align: right; vertical-align: middle; }

    .footer-doc-no {
      font-size: 9px;
      color: #718096;
    }
    .footer-doc-no b { color: #2d3748; }
    .footer-powered {
      font-size: 9px;
      color: #a0aec0;
    }
    .footer-powered b {
      color: #1a56db;
      font-weight: 700;
    }

    .bottom-bar {
      height: 4px;
      background: #1a56db;
    }
  </style>
</head>

<body>
  @php
    $logoPath = public_path('assets/img/logo_reka.png');

    $resolvePhoto = function ($p) {
      $p = trim((string)$p);
      if ($p === '') return null;
      $isUrl = \Illuminate\Support\Str::startsWith($p, ['http://', 'https://']);
      return $isUrl ? $p : public_path('storage/'.$p);
    };
  @endphp

  {{-- TOP ACCENT BAR --}}
  <div class="accent-bar"></div>

  {{-- HEADER --}}
  <div class="header">

    {{-- LEFT: Brand --}}
    <div class="header-brand">
      @if(file_exists($logoPath))
        <img class="logo-img" src="{{ $logoPath }}" alt="Logo Reka">
      @endif
      <div class="brand-name">RekaTrack</div>
      <div class="brand-tagline">Sistem Manajemen Pengiriman</div>
    </div>

    {{-- CENTER: Doc Title --}}
    <div class="header-center">
      <div class="doc-title-block">
        <div class="doc-title">BUKTI PENGIRIMAN</div>
        <div class="doc-subtitle-txt">Dokumen Resmi Penerimaan Surat Jalan</div>
      </div>
    </div>

    {{-- RIGHT: Meta --}}
    <div class="header-meta">
      <div class="meta-badge">
        <div class="meta-label">No. Surat Jalan</div>
        <div class="meta-value">{{ $travelDocument->no_travel_document ?? '-' }}</div>
        <div class="meta-value-sm">
          Dicetak: {{ $printedAt ? $printedAt->format('d M Y, H:i') : '-' }} WIB
        </div>
      </div>
    </div>

  </div>

  <div class="divider"></div>

  {{-- BODY --}}
  <div class="body-wrap">

    {{-- STATUS STRIP --}}
    @if(!empty($confirmation?->received_at))
    <div class="status-strip">
      <div class="status-icon">&#10003;</div>
      <div class="status-text">
        <div class="status-title">Pengiriman Telah Diterima</div>
        <div class="status-desc">
          Diterima oleh <b>{{ $confirmation->receiver_name ?? '-' }}</b>
          pada {{ \Carbon\Carbon::parse($confirmation->received_at)->format('d F Y, H:i') }} WIB
        </div>
      </div>
    </div>
    @endif

    {{-- ROW 1: Informasi Dokumen + Informasi Penerimaan --}}
    <div class="row">

      {{-- Informasi Dokumen --}}
      <div class="col-half">
        <div class="card">
          <div class="card-head">
            <div class="card-head-left">
              <span class="card-title">
                <span class="card-title-dot"></span>Informasi Dokumen
              </span>
            </div>
          </div>
          <div class="card-body">

            <div class="info-row">
              <div class="lbl">No Surat Jalan</div>
              <div class="val">{{ $travelDocument->no_travel_document ?? '-' }}</div>
            </div>

            <div class="info-row">
              <div class="lbl">Driver</div>
              <div class="val">
                @if($travelDocument->driver)
                  {{ $travelDocument->driver->name ?? '-' }}
                  <span class="val-sub">NIP: {{ $travelDocument->driver->nip ?? '-' }}</span>
                @else
                  -
                @endif
              </div>
            </div>

            <div class="info-row">
              <div class="lbl">Tanggal SJN</div>
              <div class="val">
                @if($travelDocument->document_date)
                  {{ \Carbon\Carbon::parse($travelDocument->document_date)->format('d F Y') }}
                  @if($travelDocument->is_backdate)
                    <span class="val-chip">Backdate</span>
                  @endif
                @else
                  -
                @endif
              </div>
            </div>

            <div class="info-row">
              <div class="lbl">Tujuan</div>
              <div class="val">{{ $travelDocument->send_to ?? '-' }}</div>
            </div>

            <div class="info-row">
              <div class="lbl">Proyek</div>
              <div class="val">{{ $travelDocument->project ?? '-' }}</div>
            </div>

          </div>
        </div>
      </div>

      {{-- Informasi Penerimaan --}}
      <div class="col-half">
        <div class="card">
          <div class="card-head">
            <div class="card-head-left">
              <span class="card-title">
                <span class="card-title-dot"></span>Informasi Penerimaan
              </span>
            </div>
          </div>
          <div class="card-body">

            <div class="info-row">
              <div class="lbl">Nama Penerima</div>
              <div class="val">{{ $confirmation->receiver_name ?? '-' }}</div>
            </div>

            <div class="info-row">
              <div class="lbl">Waktu Diterima</div>
              <div class="val">
                @if(!empty($confirmation?->received_at))
                  {{ \Carbon\Carbon::parse($confirmation->received_at)->format('d F Y') }}
                  <span class="val-sub">{{ \Carbon\Carbon::parse($confirmation->received_at)->format('H:i') }} WIB</span>
                @else
                  -
                @endif
              </div>
            </div>

            <div class="info-row">
              <div class="lbl">Mulai Pengiriman</div>
              <div class="val">
                @if($travelDocument->start_time)
                  {{ \Carbon\Carbon::parse($travelDocument->start_time)->format('d F Y') }}
                  <span class="val-sub">{{ \Carbon\Carbon::parse($travelDocument->start_time)->format('H:i') }} WIB</span>
                @else
                  -
                @endif
              </div>
            </div>

            <div class="info-row">
              <div class="lbl">Durasi Pengiriman</div>
              <div class="val">
                @if($travelDocument->start_time && $travelDocument->end_time)
                  {{ \Carbon\Carbon::parse($travelDocument->start_time)->diffForHumans(\Carbon\Carbon::parse($travelDocument->end_time), true) }}
                @else
                  -
                @endif
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <div class="spacer"></div>

    {{-- ROW 2: Foto + Catatan --}}
    <div class="row">

      {{-- Bukti Foto --}}
      <div class="col-half">
        <div class="card">
          <div class="card-head">
            <div class="card-head-left">
              <span class="card-title">
                <span class="card-title-dot"></span>Bukti Foto Pengiriman
              </span>
            </div>
            <div class="card-head-right">
              <span class="card-badge">{{ is_array($photos) ? count($photos) : 0 }} foto</span>
            </div>
          </div>
          <div class="card-body">
            @if(is_array($photos) && count($photos))
              <div class="photos-wrap">
                @foreach($photos as $p)
                  @php $src = $resolvePhoto($p); @endphp
                  @if($src)
                    <div class="photo-cell">
                      <div class="photo-frame">
                        <img src="{{ $src }}" alt="Foto bukti pengiriman">
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>
            @else
              <div class="photo-empty">Tidak ada foto tersedia.</div>
            @endif
          </div>
        </div>
      </div>

      {{-- Catatan Penerima --}}
      <div class="col-half">
        <div class="card">
          <div class="card-head">
            <div class="card-head-left">
              <span class="card-title">
                <span class="card-title-dot"></span>Catatan Penerima
              </span>
            </div>
          </div>
          <div class="card-body">
            <div class="note-box">
              @if(!empty($confirmation->note))
                {!! nl2br(e($confirmation->note)) !!}
              @else
                <span class="note-empty">Tidak ada catatan dari penerima.</span>
              @endif
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>

  {{-- FOOTER --}}
  <div class="footer-divider"></div>
  <div class="footer">
    <div class="footer-left">
      <div class="footer-doc-no">
        Dokumen: <b>{{ $travelDocument->no_travel_document ?? '-' }}</b>
        &nbsp;&bull;&nbsp; Dicetak {{ $printedAt ? $printedAt->format('d M Y, H:i') : '-' }} WIB
      </div>
    </div>
    <div class="footer-right">
      <div class="footer-powered">Powered by <b>RekaTrack</b></div>
    </div>
  </div>

  <div class="bottom-bar"></div>

</body>
</html>
