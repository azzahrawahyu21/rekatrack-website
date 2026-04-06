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

          <div class="d-flex gap-2">
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

        <div class="row g-3">
          <div class="col-md-4">
            <div class="p-3 border rounded">
              <div class="text-muted">Status</div>
              <div class="fs-6">
                <span id="statusBadge" class="badge badge-secondary">-</span>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="p-3 border rounded">
              <div class="text-muted">Titik Lokasi</div>
              <div class="fs-6">
                <strong id="totalPoints">0</strong> titik
              </div>
              <div class="small text-muted mt-1">Ditampilkan setelah filter duplikat.</div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="p-3 border rounded">
              <div class="text-muted">Update Terakhir</div>
              <div class="fs-6">
                <strong id="lastTime">-</strong>
              </div>
              <div class="small text-muted mt-1">
                Lat: <span id="lastLat">-</span> | Lng: <span id="lastLng">-</span>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3 small text-muted">
          Auto-refresh data: <strong id="pollState">ON</strong> (setiap <span id="pollEvery">10</span> detik)
          <button id="btnTogglePoll" class="btn btn-sm btn-link ms-2 p-0 align-baseline" type="button">Matikan</button>
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
  let gpsLine = null;      // polyline from points (DISABLED / removed)
  let roadLine = null;     // ORS polyline (keep)
  let pointsLayer = null;  // titik oranye
  let lastRouteSig = '';   // to avoid re-request ORS on same endpoints
  let lastEndLatLng = null;

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

  function setStatusBadge(statusText) {
    const badge = document.getElementById('statusBadge');
    if (!badge) return;

    const s = (statusText || '').toString();
    badge.textContent = s || '-';

    const lower = s.toLowerCase();
    badge.className = 'badge';

    if (!s) badge.classList.add('badge-secondary');
    else if (lower.includes('sedang')) badge.classList.add('badge-info');
    else if (lower.includes('terkirim')) badge.classList.add('badge-success');
    else badge.classList.add('badge-secondary');
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
  // DATA HELPERS
  // =========================
  function normalizeLocations(locations) {
    // Ensure numeric and sorted by timestamp asc, keep locations and latLngs aligned
    const sorted = [...locations].sort((a,b) => new Date(a.timestamp) - new Date(b.timestamp));
    const filtered = sorted.filter(l => l && l.latitude != null && l.longitude != null);
    const latLngs = filtered.map(l => [parseFloat(l.latitude), parseFloat(l.longitude)]);
    return { filtered, latLngs };
  }

  function signatureForRoute(latLngs) {
    // Use first+last for signature to prevent repeated ORS calls
    if (!latLngs?.length) return '';
    const a = latLngs[0];
    const b = latLngs[latLngs.length - 1];
    return `${a[0].toFixed(5)},${a[1].toFixed(5)}|${b[0].toFixed(5)},${b[1].toFixed(5)}`;
  }

  function setInfo(locations, status) {
    setStatusBadge(status);

    const total = document.getElementById('totalPoints');
    if (total) total.textContent = String(locations?.length || 0);

    const last = locations?.length ? locations[locations.length - 1] : null;
    document.getElementById('lastTime').textContent = fmtTime(last?.timestamp);
    document.getElementById('lastLat').textContent = last?.latitude ?? '-';
    document.getElementById('lastLng').textContent = last?.longitude ?? '-';
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
    // garis lurus (gpsLine) DISABLED, tapi kalau sempat tergambar tetap kita hapus
    if (gpsLine) { map.removeLayer(gpsLine); gpsLine = null; }
    if (roadLine) { map.removeLayer(roadLine); roadLine = null; }
  }

  function clearPointsOnly() {
    if (pointsLayer) pointsLayer.clearLayers();
  }

  function updateMarkers(latLngs, locations) {
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
    endMarker.bindPopup(`
      <strong>🚚 Lokasi Terakhir</strong><br>
      ${last?.latitude ?? '-'}, ${last?.longitude ?? '-'}<br>
      <small>${fmtTime(last?.timestamp)}</small>
    `);

    // open popup only when end moved significantly (prevents annoying reopen every poll)
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

      L.circleMarker(p, {
        radius: 5,
        weight: 2,
        color: '#ff7a00',
        fillColor: '#ff7a00',
        fillOpacity: 0.9
      })
      .addTo(pointsLayer)
      .bindPopup(`
        <strong>📍 Titik #${idx + 1}</strong><br>
        ${loc?.latitude ?? p[0]}, ${loc?.longitude ?? p[1]}<br>
        <small>${fmtTime(loc?.timestamp)}</small>
      `);
    });
  }

  function focusRoute() {
    if (roadLine) {
      map.fitBounds(roadLine.getBounds(), { padding: [60, 60] });
      return;
    }

    // fallback: fit all orange points
    if (pointsLayer && pointsLayer.getLayers().length) {
      const group = L.featureGroup(pointsLayer.getLayers());
      map.fitBounds(group.getBounds(), { padding: [60, 60] });
      return;
    }

    if (endMarker) map.setView(endMarker.getLatLng(), 15);
  }

  async function drawRoadRouteIfNeeded(latLngs) {
    // ORS request can be heavy; only do it when endpoints changed and points >= 2
    if (!latLngs || latLngs.length < 2) return;

    const sig = signatureForRoute(latLngs);
    if (!sig || sig === lastRouteSig) return;
    lastRouteSig = sig;

    // Keep payload limited (avoid ORS fail)
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

      if (roadLine) roadLine.setLatLngs(roadPath);
      else roadLine = L.polyline(roadPath, { weight: 5, opacity: 1 }).addTo(map);

    } catch (e) {
      // If ORS fails, we still have pointsLayer & markers
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
      setInfo(locationsRaw, data.status);

      if (!locationsRaw.length) {
        // hapus garis & titik
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

      // Update marker start/end
      updateMarkers(latLngs, filtered);

      // HILANGKAN garis lurus antar titik (gpsLine)
      if (gpsLine) { map.removeLayer(gpsLine); gpsLine = null; }

      // Tampilkan titik oranye
      drawOrangePoints(latLngs, filtered);

      // Tetap gunakan roadLine (ORS)
      await drawRoadRouteIfNeeded(latLngs);

      // First load: fit to route
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
