{{-- resources/views/General/tracker-show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Tracking | RekaTrack')
@php($pageName = 'Detail Tracking')

@section('content')
<div class="row">
  {{-- Info Panel --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
          <div>
            <h5 class="mb-1">Detail Tracking</h5>
            <div class="text-muted">
              Nomor SJN: <strong id="sjnText">{{ $travelDocument->no_travel_document ?? '-' }}</strong>
            </div>
          </div>

          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('tracking.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>

            <a href="{{ route('shippings.detail', $travelDocument->id) }}" class="btn btn-primary">
              <i class="fas fa-truck me-1"></i> Detail Pengiriman
            </a>

            <button id="btnFocus" class="btn btn-outline-primary" type="button">
              <i class="fas fa-crosshairs me-1"></i> Fokus Rute
            </button>
          </div>
        </div>

        <hr class="my-3"/>

        {{-- Kotak info (rapi & seragam) --}}
        <div class="row g-3 stats-row">
          {{-- STATUS (premium + animated) --}}
          <div class="col-md-4">
            <div class="stat-card h-100 stat-card-status">
              <div class="stat-title">Status</div>

              <div class="status-wrap">
                <span id="statusBadge" class="status-pill status-pill--neutral">
                  <span class="status-icon">
                    <i class="fas fa-minus"></i>
                  </span>
                  <span class="status-text">-</span>
                  <span class="status-shine" aria-hidden="true"></span>
                </span>
              </div>

              <div class="status-hint text-muted small">
                <span class="dot-live" aria-hidden="true"></span>
                Live update
              </div>
            </div>
          </div>

          {{-- TITIK --}}
          <div class="col-md-4">
            <div class="stat-card h-100">
              <div class="stat-title">Titik Lokasi</div>
              <div class="stat-value">
                <strong id="totalPoints">0</strong> titik
              </div>
              <div class="stat-sub">Ditampilkan setelah filter duplikat.</div>
            </div>
          </div>

          {{-- UPDATE TERAKHIR --}}
          <div class="col-md-4">
            <div class="stat-card h-100">
              <div class="stat-title">Update Terakhir</div>
              <div class="stat-value"><strong id="lastTime">-</strong></div>

              <div class="stat-sub">
                <span class="me-2">Lat: <span id="lastLat">-</span></span>
                <span>| Lng: <span id="lastLng">-</span></span>
              </div>

              <div class="stat-address">
                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                <span id="lastAddress">-</span>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3 small text-muted d-flex align-items-center flex-wrap gap-2">
          <span>Auto-refresh data: <strong id="pollState">ON</strong> (setiap <span id="pollEvery">10</span> detik)</span>
          <button id="btnTogglePoll" class="btn btn-sm btn-link p-0 align-baseline" type="button">Matikan</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Map --}}
  <div class="col-12">
    <div class="card">
      <div class="card-body p-0" style="height: 650px; position: relative;">
        <div id="map" class="w-100 h-100"></div>

        {{-- Lightweight loading overlay --}}
        <div id="loadingOverlay" style="
          position:absolute; inset:0; display:none; align-items:center; justify-content:center;
          background: rgba(255,255,255,0.6); z-index: 999;">
          <div class="px-3 py-2 bg-white border rounded shadow-sm">
            <i class="fas fa-spinner fa-spin me-1"></i> Memuat data tracking...
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map { height: 100%; width: 100%; border-radius: 0.5rem; }
  .leaflet-container { z-index: 1; }

  /* ====== Stats cards (kotak info) ====== */
  .stats-row { align-items: stretch; }

  .stat-card{
    background: #fff;
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .stat-title{
    font-size: 12px;
    letter-spacing: .2px;
    color: #6c757d;
    font-weight: 700;
    text-transform: uppercase;
  }

  .stat-value{
    font-size: 16px;
    font-weight: 800;
    color: #111827;
    line-height: 1.2;
    min-height: 22px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .stat-sub{
    font-size: 12px;
    color: #6c757d;
  }

  .stat-address{
    margin-top: 6px;
    padding-top: 8px;
    border-top: 1px dashed rgba(0,0,0,.12);
    font-size: 12px;
    color: #374151;
    display: flex;
    align-items: flex-start;
    gap: 6px;

    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  #btnTogglePoll{
    text-decoration: none;
    font-weight: 700;
  }

  @media (max-width: 576px){
    .stat-card{ padding: 12px 14px; }
    .stat-value{ font-size: 15px; }
  }

  /* ============================
     PREMIUM STATUS PILL
     ============================ */
  .stat-card-status{
    position: relative;
  }

  .status-wrap{
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex: 1;
    margin-top: 4px;
  }

  .status-pill{
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    border-radius: 999px;
    color: #fff;
    font-weight: 800;
    letter-spacing: .2px;
    font-size: 14px;
    box-shadow:
      0 8px 18px rgba(0,0,0,.10),
      0 1px 0 rgba(255,255,255,.2) inset;
    overflow: hidden;
    transform: translateZ(0);
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
  }

  .status-pill:hover{
    transform: translateY(-1px);
    box-shadow:
      0 10px 22px rgba(0,0,0,.14),
      0 1px 0 rgba(255,255,255,.25) inset;
  }

  .status-icon{
    width: 28px;
    height: 28px;
    border-radius: 999px;
    background: rgba(255,255,255,.18);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 0 rgba(255,255,255,.15) inset;
  }

  .status-text{
    white-space: nowrap;
  }

  /* shine sweep animation */
  .status-shine{
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.22) 35%, transparent 70%);
    transform: translateX(-120%);
    animation: shine 2.8s ease-in-out infinite;
    pointer-events: none;
  }

  @keyframes shine{
    0%   { transform: translateX(-120%); opacity: .0; }
    20%  { opacity: .35; }
    50%  { transform: translateX(120%); opacity: .25; }
    100% { transform: translateX(120%); opacity: 0; }
  }

  /* tiny live dot */
  .status-hint{
    margin-top: 8px;
    display: inline-flex;
    gap: 8px;
    align-items: center;
    justify-content: flex-end;
  }
  .dot-live{
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16,185,129,.55);
    animation: pulse 1.6s infinite;
  }

  @keyframes pulse{
    0% { box-shadow: 0 0 0 0 rgba(16,185,129,.55); }
    70% { box-shadow: 0 0 0 10px rgba(16,185,129,0); }
    100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
  }

  /* color variants (gradient) */
  .status-pill--neutral{ background: linear-gradient(135deg, #64748b, #475569); }
  .status-pill--info{    background: linear-gradient(135deg, #06b6d4, #2563eb); }
  .status-pill--success{ background: linear-gradient(135deg, #22c55e, #16a34a); }
  .status-pill--warning{ background: linear-gradient(135deg, #f59e0b, #f97316); }
  .status-pill--danger{  background: linear-gradient(135deg, #ef4444, #dc2626); }

  /* animate when status changes */
  .status-pill.is-updated{
    animation: pop .25s ease-out;
  }
  @keyframes pop{
    0% { transform: scale(.98); filter: saturate(.9); }
    100% { transform: scale(1); filter: saturate(1); }
  }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  // =========================
  // CONFIG
  // =========================
  const SJN = @json($travelDocument->no_travel_document ?? '');
  const SEARCH_URL = @json(route('tracking.search'));
  const ROUTE_URL  = @json(route('tracking.route'));
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // Polling
  const POLL_SECONDS = 10;
  let pollEnabled = true;
  let pollTimer = null;

  // Map state
  let map;
  let startMarker = null;
  let endMarker = null;
  let gpsLine = null;      // DISABLED
  let roadLine = null;     // keep
  let pointsLayer = null;  // orange points
  let lastRouteSig = '';
  let lastEndLatLng = null;

  // status state (to animate only when changed)
  let _lastStatusText = '';

  // =========================
  // ICONS
  // =========================
  function iconRed() {
    return L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41]
    });
  }

  function iconGreen() {
    return L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41]
    });
  }

  // =========================
  // UI HELPERS
  // =========================
  function setLoading(isLoading) {
    const el = document.getElementById('loadingOverlay');
    if (el) el.style.display = isLoading ? 'flex' : 'none';
  }

  function fmtTime(ts) {
    if (!ts) return '-';
    try {
      const d = new Date(ts);
      if (isNaN(d.getTime())) return ts;
      const pad = (n) => String(n).padStart(2, '0');
      return `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    } catch {
      return ts;
    }
  }

  function notify(type, message) {
    if (window.$?.notify) {
      $.notify({ message }, { type, placement: { from: 'top', align: 'right' }, time: 4000 });
    } else {
      console[type === 'error' ? 'error' : 'log'](message);
    }
  }

  // =========================
  // PREMIUM STATUS BADGE
  // =========================
  function setStatusBadge(statusText) {
    const pill = document.getElementById('statusBadge');
    if (!pill) return;

    const textEl = pill.querySelector('.status-text');
    const iconEl = pill.querySelector('.status-icon i');

    const s = (statusText || '').toString().trim();
    const shown = s || '-';

    // update text
    if (textEl) textEl.textContent = shown;
    else pill.textContent = shown;

    // reset class
    pill.classList.remove(
      'status-pill--neutral','status-pill--info','status-pill--success','status-pill--warning','status-pill--danger'
    );

    const lower = shown.toLowerCase();

    let variant = 'status-pill--neutral';
    let icon = 'fa-minus';

    if (!s) {
      variant = 'status-pill--neutral';
      icon = 'fa-minus';
    } else if (lower.includes('sedang')) {
      variant = 'status-pill--info';
      icon = 'fa-truck';
    } else if (lower.includes('terkirim')) {
      variant = 'status-pill--success';
      icon = 'fa-check';
    } else if (lower.includes('belum terkirim')) {
      variant = 'status-pill--warning';
      icon = 'fa-clock';
    } else if (lower.includes('batal') || lower.includes('gagal')) {
      variant = 'status-pill--danger';
      icon = 'fa-times';
    }

    pill.classList.add(variant);
    if (iconEl) {
      iconEl.className = `fas ${icon}`;
    }

    // animate only when status changes
    if (shown !== _lastStatusText) {
      pill.classList.remove('is-updated');
      void pill.offsetWidth; // reflow
      pill.classList.add('is-updated');
      _lastStatusText = shown;
    }
  }

  // =========================
  // REVERSE GEOCODE (Nominatim)
  // =========================
  const geocodeCache = new Map();
  const GEOCODE_TTL_MS = 1000 * 60 * 60; // 1 hour
  let lastGeocodeAt = 0;

  function roundCoord(n, digits = 5) {
    const p = Math.pow(10, digits);
    return Math.round(n * p) / p;
  }

  function cacheKey(lat, lng) {
    return `${roundCoord(lat, 5)},${roundCoord(lng, 5)}`;
  }

  async function reverseGeocode(lat, lng) {
    if (lat == null || lng == null) return null;

    const key = cacheKey(lat, lng);
    const now = Date.now();

    const cached = geocodeCache.get(key);
    if (cached && (now - cached.ts) < GEOCODE_TTL_MS) return cached.address;

    const gap = now - lastGeocodeAt;
    if (gap < 1200) await new Promise(r => setTimeout(r, 1200 - gap));
    lastGeocodeAt = Date.now();

    try {
      const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&zoom=18&addressdetails=1`;
      const res = await fetch(url, {
        headers: {
          'Accept': 'application/json',
          'User-Agent': 'RekaTrack/1.0 (reverse-geocode)'
        }
      });
      if (!res.ok) return null;

      const json = await res.json();
      const address = json?.display_name || null;

      geocodeCache.set(key, { address, ts: Date.now() });
      return address;
    } catch (e) {
      console.warn('reverseGeocode failed:', e);
      return null;
    }
  }

  function setLastAddress(text) {
    const el = document.getElementById('lastAddress');
    if (el) el.textContent = text || '-';
  }

  // =========================
  // DATA HELPERS
  // =========================
  function normalizeLocations(locations) {
    const sorted = [...locations].sort((a,b) => new Date(a.timestamp) - new Date(b.timestamp));
    const filtered = sorted.filter(l => l && l.latitude != null && l.longitude != null);
    const latLngs = filtered.map(l => [parseFloat(l.latitude), parseFloat(l.longitude)]);
    return { filtered, latLngs };
  }

  function signatureForRoute(latLngs) {
    if (!latLngs?.length) return '';
    const a = latLngs[0];
    const b = latLngs[latLngs.length - 1];
    return `${a[0].toFixed(5)},${a[1].toFixed(5)}|${b[0].toFixed(5)},${b[1].toFixed(5)}`;
  }

  async function setInfo(locations, status) {
    setStatusBadge(status);

    const total = document.getElementById('totalPoints');
    if (total) total.textContent = String(locations?.length || 0);

    const last = locations?.length ? locations[locations.length - 1] : null;
    document.getElementById('lastTime').textContent = fmtTime(last?.timestamp);
    document.getElementById('lastLat').textContent = last?.latitude ?? '-';
    document.getElementById('lastLng').textContent = last?.longitude ?? '-';

    const lat = last?.latitude != null ? parseFloat(last.latitude) : null;
    const lng = last?.longitude != null ? parseFloat(last.longitude) : null;

    if (lat != null && lng != null) {
      setLastAddress('Mencari alamat...');
      const addr = await reverseGeocode(lat, lng);
      setLastAddress(addr || '-');
    } else {
      setLastAddress('-');
    }
  }

  // =========================
  // MAP INIT & UPDATE
  // =========================
  function initMap() {
    map = L.map('map').setView([-2.5489, 118.0132], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    document.getElementById('btnFocus')?.addEventListener('click', () => focusRoute());
  }

  function clearLinesOnly() {
    if (gpsLine) { map.removeLayer(gpsLine); gpsLine = null; }
    if (roadLine) { map.removeLayer(roadLine); roadLine = null; }
  }

  function clearPointsOnly() {
    if (pointsLayer) pointsLayer.clearLayers();
  }

  async function updateMarkers(latLngs, locations) {
    if (!latLngs.length) return;

    const start = latLngs[0];
    const end = latLngs[latLngs.length - 1];

    if (!startMarker) {
      startMarker = L.marker(start, { icon: iconGreen() }).addTo(map).bindPopup('📍 Titik Awal');
    } else {
      startMarker.setLatLng(start);
    }

    if (!endMarker) {
      endMarker = L.marker(end, { icon: iconRed() }).addTo(map);
    } else {
      endMarker.setLatLng(end);
    }

    const last = locations?.length ? locations[locations.length - 1] : null;

    let addressText = '-';
    if (last?.latitude != null && last?.longitude != null) {
      const addr = await reverseGeocode(parseFloat(last.latitude), parseFloat(last.longitude));
      addressText = addr || '-';
    }

    endMarker.bindPopup(`
      <strong>🚚 Lokasi Terakhir</strong><br>
      ${last?.latitude ?? '-'}, ${last?.longitude ?? '-'}<br>
      <small>${fmtTime(last?.timestamp)}</small><br>
      <small><i class="fas fa-map-marker-alt"></i> ${addressText}</small>
    `);

    const moved = !lastEndLatLng || (lastEndLatLng[0] !== end[0] || lastEndLatLng[1] !== end[1]);
    if (moved) {
      endMarker.openPopup();
      lastEndLatLng = end;
    }
  }

  function drawOrangePoints(latLngs, locations) {
    if (!pointsLayer) pointsLayer = L.layerGroup().addTo(map);
    pointsLayer.clearLayers();

    latLngs.forEach((p, idx) => {
      const loc = locations?.[idx];

      const marker = L.circleMarker(p, {
        radius: 5,
        weight: 2,
        color: '#ff7a00',
        fillColor: '#ff7a00',
        fillOpacity: 0.9
      }).addTo(pointsLayer);

      const popupId = `addr-${idx}`;
      marker.bindPopup(`
        <strong>📍 Titik #${idx + 1}</strong><br>
        ${loc?.latitude ?? p[0]}, ${loc?.longitude ?? p[1]}<br>
        <small>${fmtTime(loc?.timestamp)}</small><br>
        <small><i class="fas fa-map-marker-alt"></i> <span id="${popupId}">Klik untuk alamat...</span></small>
      `);

      marker.on('popupopen', async () => {
        const lat = loc?.latitude != null ? parseFloat(loc.latitude) : p[0];
        const lng = loc?.longitude != null ? parseFloat(loc.longitude) : p[1];
        const addr = await reverseGeocode(lat, lng);

        const el = document.getElementById(popupId);
        if (el) el.textContent = addr || '-';
      });
    });
  }

  function focusRoute() {
    if (roadLine) {
      map.fitBounds(roadLine.getBounds(), { padding: [60, 60] });
      return;
    }

    if (pointsLayer && pointsLayer.getLayers().length) {
      const group = L.featureGroup(pointsLayer.getLayers());
      map.fitBounds(group.getBounds(), { padding: [60, 60] });
      return;
    }

    if (endMarker) map.setView(endMarker.getLatLng(), 15);
  }

  async function drawRoadRouteIfNeeded(latLngs) {
    if (!latLngs || latLngs.length < 2) return;

    const sig = signatureForRoute(latLngs);
    if (!sig || sig === lastRouteSig) return;
    lastRouteSig = sig;

    let waypoints = latLngs;
    const MAX_WP = 50;
    if (waypoints.length > MAX_WP) {
      const step = Math.ceil(waypoints.length / MAX_WP);
      const sampled = [];
      for (let i = 0; i < waypoints.length; i += step) sampled.push(waypoints[i]);
      if (sampled[sampled.length - 1] !== waypoints[waypoints.length - 1]) {
        sampled.push(waypoints[waypoints.length - 1]);
      }
      waypoints = sampled;
    }

    try {
      const res = await fetch(ROUTE_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}),
        },
        body: JSON.stringify({ waypoints })
      });

      const route = await res.json();
      const coords = route?.features?.[0]?.geometry?.coordinates;
      if (!coords?.length) return;

      const roadPath = coords.map(c => [c[1], c[0]]);

      if (roadLine) {
        roadLine.setLatLngs(roadPath);
        roadLine.setStyle({ color: '#ff0000', weight: 6, opacity: 0.9, lineJoin: 'round' });
      } else {
        roadLine = L.polyline(roadPath, {
          weight: 6,
          opacity: 0.9,
          lineJoin: 'round'
        }).addTo(map);
      }

    } catch (e) {
      console.warn('ORS route failed:', e);
    }
  }

  // =========================
  // FETCH & RENDER
  // =========================
  async function fetchTracking() {
    if (!SJN) {
      notify('error', 'Nomor SJN tidak valid.');
      return;
    }

    setLoading(true);
    try {
      const url = `${SEARCH_URL}?no_travel_document=${encodeURIComponent(SJN)}`;
      const res = await fetch(url);
      const data = await res.json();

      if (!data?.success) {
        setLoading(false);
        notify('error', data?.message || 'Gagal memuat data tracking.');
        return;
      }

      const locationsRaw = Array.isArray(data.locations) ? data.locations : [];
      await setInfo(locationsRaw, data.status);

      if (!locationsRaw.length) {
        clearLinesOnly();
        clearPointsOnly();
        setLoading(false);
        notify('warning', 'Belum ada data lokasi untuk pengiriman ini.');
        return;
      }

      const { filtered, latLngs } = normalizeLocations(locationsRaw);

      if (!latLngs.length) {
        clearLinesOnly();
        clearPointsOnly();
        setLoading(false);
        notify('warning', 'Data lokasi tidak valid (lat/lng kosong).');
        return;
      }

      await updateMarkers(latLngs, filtered);

      if (gpsLine) { map.removeLayer(gpsLine); gpsLine = null; }

      drawOrangePoints(latLngs, filtered);

      await drawRoadRouteIfNeeded(latLngs);

      if (!fetchTracking._didFitOnce) {
        fetchTracking._didFitOnce = true;
        focusRoute();
      }

      setLoading(false);
    } catch (e) {
      setLoading(false);
      notify('error', 'Gagal memuat tracking (network/server).');
      console.error(e);
    }
  }

  // =========================
  // POLLING CONTROL
  // =========================
  function startPolling() {
    document.getElementById('pollEvery').textContent = String(POLL_SECONDS);
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(() => {
      if (pollEnabled) fetchTracking();
    }, POLL_SECONDS * 1000);
  }

  function updatePollUI() {
    const state = document.getElementById('pollState');
    const btn = document.getElementById('btnTogglePoll');
    if (state) state.textContent = pollEnabled ? 'ON' : 'OFF';
    if (btn) btn.textContent = pollEnabled ? 'Matikan' : 'Nyalakan';
  }

  // =========================
  // BOOT
  // =========================
  document.addEventListener('DOMContentLoaded', async () => {
    initMap();
    updatePollUI();

    document.getElementById('btnTogglePoll')?.addEventListener('click', () => {
      pollEnabled = !pollEnabled;
      updatePollUI();
      if (pollEnabled) fetchTracking();
    });

    await fetchTracking();
    startPolling();
  });
</script>
@endpush
