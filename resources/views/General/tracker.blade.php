@extends('layouts.app')

@section('title', 'Tracking Pengiriman - RekaTrack')
@php($pageName = 'Tracking Pengiriman')

@section('content')
    <!-- === Statistik: Sesuai Gaya Kaiadmin === -->
    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Sedang Dikirim</p>
                                <h4 class="card-title">{{ $totalActive }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-6 col-lg-6">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Terkirim</p>
                                <h4 class="card-title">{{ $totalDelivered }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === Pencarian Manual === -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Cari Pengiriman</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input
                                    type="text"
                                    id="search"
                                    class="form-control"
                                    placeholder="Cari berdasarkan Nomor Surat Jalan..."
                                />
                                <button onclick="searchTracking()" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === Pengiriman Aktif === -->
    @if($activeShippings->isNotEmpty())
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Pengiriman Aktif</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nomor SJN</th>
                                        <th>Kepada</th>
                                        <th>Proyek</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeShippings as $shipping)
                                        <tr style="cursor: pointer;" onclick="goDetail('{{ $shipping->id }}')">
                                            <td class="text-primary fw-bold">{{ $shipping->no_travel_document }}</td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                {{ $shipping->send_to ?: '-' }}
                                            </td>
                                            <td>{{ $shipping->project ?: '-' }}</td>
                                            <td>
                                                {{-- @if($shipping->date_no_travel_document)
                                                    {{ \Carbon\Carbon::parse($shipping->date_no_travel_document)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif --}}
                                                @if($shipping->posting_date)
                                                    {{ \Carbon\Carbon::parse($shipping->posting_date)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">Sedang dikirim</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- === Pengiriman Terkirim === -->
    @if($deliveredShippings->isNotEmpty())
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Pengiriman Terkirim</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nomor SJN</th>
                                        <th>Kepada</th>
                                        <th>Proyek</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deliveredShippings as $shipping)
                                        <tr style="cursor: pointer;" onclick="goDetail('{{ $shipping->id }}')">
                                            <td class="text-primary fw-bold">{{ $shipping->no_travel_document }}</td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                {{ $shipping->send_to ?: '-' }}
                                            </td>
                                            <td>{{ $shipping->project ?: '-' }}</td>
                                            <td>
                                                @if($shipping->posting_date)
                                                    {{ \Carbon\Carbon::parse($shipping->posting_date)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success">Terkirim</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- === Peta === -->
    {{-- <div class="row mt-4"> --}}
    <div class="row mt-4" id="map-section">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0" style="height: 700px; position: relative;">
                    <div id="map" class="w-100 h-100"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 100%; width: 100%; border-radius: 0.5rem; }
        .geocode-popup {
            min-width: 200px;
            text-align: center;
            padding: 8px !important;
        }
        table tr:hover {
            background-color: #f8f9fa !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        const map = L.map("map").setView([-2.5489, 118.0132], 5);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 18,
            // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // ICON
        function greenIcon() {
            return L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            });
        }

        function redIcon() {
            return L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            });
        }

        function homeIcon() {
            return L.icon({
                iconUrl: '/images/logo/home-icon.png', // icon truk warna merah
                iconSize: [42, 42],
                iconAnchor: [21, 21],
                popupAnchor: [0, -20]
            });
        }

        // CLEAR MAP
        function clearMap() {
            map.eachLayer(layer => {
                if (!(layer instanceof L.TileLayer)) map.removeLayer(layer);
            });
        }

        function loadTracking(sjn) {
            fetch(`{{ route('tracking.search') }}?search=${encodeURIComponent(sjn)}`)
                .then(res => res.json())
                .then(async data => {

                    if (!data.success || !data.locations?.length) {
                        showAlert('error', data.message || 'Tidak ada data lokasi');
                        return;
                    }

                    scrollToMap();

                    data.locations.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

                    const locations = data.locations;
                    const latLngs = locations.map(l => [parseFloat(l.latitude), parseFloat(l.longitude)]);
                    const lastIdx = latLngs.length - 1;

                    clearMap();

                    // MARKER AWAL
                    const startMarker = L.marker(latLngs[0], { icon: homeIcon() }).addTo(map);
                    showLoadingPopup(startMarker);
                    startMarker.setPopupContent(`
                        <strong>🚚 Titik Awal</strong><br>
                        ${await getReverseGeocode(latLngs[0][0], latLngs[0][1])}<br>
                        ${locations[0].timestamp}
                    `);

                    // CHECKPOINT
                    if (latLngs.length > 2) {
                        for (let i = 1; i < lastIdx; i++) {
                            const cp = L.circleMarker(latLngs[i], {
                                radius: 2,
                                color: '#ff7800',
                                fillOpacity: 0.8
                            }).addTo(map);

                            cp.bindPopup(`
                                <strong>Checkpoint ${i}</strong><br>
                                ${await getReverseGeocode(latLngs[i][0], latLngs[i][1])}<br>
                                ${locations[i].timestamp}
                            `);
                        }
                    }

                    // MARKER AKHIR / BERJALAN
                    const isActive = data.status?.toLowerCase().includes('sedang');
                    const endLabel = isActive ? '🚚 Lokasi Berjalan' : '🏁 Titik Akhir';

                    // const endMarker = L.marker(latLngs[lastIdx], { icon: redIcon() }).addTo(map);
                    const endMarker = L.marker(latLngs[lastIdx], { icon: redIcon() }).addTo(map);
                    showLoadingPopup(endMarker);
                    endMarker.setPopupContent(`
                        <strong>${endLabel}</strong><br>
                        ${await getReverseGeocode(latLngs[lastIdx][0], latLngs[lastIdx][1])}<br>
                        ${locations[lastIdx].timestamp}
                    `).openPopup();

                    // ROUTE ORS (SATU-SATUNYA)
                    if (latLngs.length > 1) {
                        fetch('{{ route("tracking.route") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf
                            },
                            body: JSON.stringify({ waypoints: latLngs })
                        })
                        .then(r => r.json())
                        .then(route => {
                            const roadPath = route.features[0].geometry.coordinates
                                .map(c => [c[1], c[0]]);

                            const roadLine = L.polyline(roadPath, {
                                color: '#1572e8',
                                weight: 5
                            }).addTo(map);

                            map.fitBounds(roadLine.getBounds(), { padding: [60, 60] });
                        });
                    } else {
                        map.setView(latLngs[0], 16);
                    }

                })
                .catch(() => showAlert('error', 'Gagal memuat tracking'));
        }

        // REALTIME (TANPA GARIS GPS)
        function renderTrackingRealtime(locations, status) {
            clearMap();

            locations.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
            const latLngs = locations.map(l => [parseFloat(l.latitude), parseFloat(l.longitude)]);

            const startMarker = L.marker(latLngs[0], { icon: greenIcon() })
                .addTo(map)
                .bindPopup('📍 Titik Awal')
                .openPopup();

            if (latLngs.length === 1) {
                map.setView(latLngs[0], 15);
                return;
            }

            const isActive = status.toLowerCase().includes('sedang');
            const lastLabel = isActive ? '🚚 Lokasi Berjalan' : '🏁 Titik Akhir';

            L.marker(latLngs.at(-1), { icon: redIcon() })
                .addTo(map)
                .bindPopup(lastLabel)
                .openPopup();

            map.setView(latLngs.at(-1), 15);
        }

        // function searchTracking() {
        //     const query = document.getElementById("search")?.value?.trim();
        //     if (!query) {
        //         showAlert('warning', 'Masukkan nomor surat jalan');
        //         return;
        //     }
        //     loadTracking(query);
        // }
        function searchTracking() {
            const sjn = document.getElementById("search")?.value?.trim();
            if (!sjn) return showAlert('warning', 'Masukkan nomor surat jalan');
            goDetail(sjn);
        }

        // async function getReverseGeocode(lat, lng) {
        //     try {
        //         const response = await fetch(
        //             `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=id`
        //         );
        //         const data = await response.json();
        //         if (data.display_name) {
        //             let addr = data.display_name;
        //             if (addr.length > 70) addr = addr.substring(0, 70) + '...';
        //             return addr;
        //         }
        //         return "Alamat tidak ditemukan";
        //     } catch (error) {
        //         console.error("Geocode error:", error);
        //         return "Alamat tidak tersedia";
        //     }
        // }
        async function getReverseGeocode(lat, lng) {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
                const data = await res.json();
                return data.display_name || 'Alamat tidak ditemukan';
            } catch {
                return 'Alamat tidak tersedia';
            }
        }

        function showLoadingPopup(marker, text = "Memuat...") {
            marker.bindPopup(`
                <div class="geocode-popup">
                    <i class="fas fa-spinner fa-spin me-1"></i> ${text}
                </div>
            `);
        }

        function showAlert(type, message) {
            if (window.$.notify) {
                $.notify({ message }, { type, placement: { from: 'top', align: 'right' }, time: 4000 });
            } else {
                alert(message);
            }
        }

        document.getElementById('search')?.addEventListener('keypress', e => {
            if (e.key === 'Enter') searchTracking();
        });

        function scrollToMap() {
            const el = document.getElementById('map-section');
            if (!el) return;

            el.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Pastikan Leaflet resize dengan benar
            setTimeout(() => {
                map.invalidateSize();
            }, 500);
        }

        function goDetail(id) {
            const base = @json(url('/tracking'));
            window.location.href = `${base}/${encodeURIComponent(id)}`;
        }

        const ACTIVE_LAST_URL = @json(route('tracking.active-last'));
        const markersById = new Map(); // id => marker

        async function fetchActiveLast() {
            try {
                const res = await fetch(ACTIVE_LAST_URL, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                if (!json.success) return;

                const items = json.data || [];
                const activeSet = new Set(items.map(i => String(i.id)));

                for (const item of items) {
                    const id = item.id;
                    const sjn = item.no_travel_document;
                    const loc = item.last_location;
                    if (!id || !loc) continue;

                    const latlng = [parseFloat(loc.latitude), parseFloat(loc.longitude)];
                    if (!isFinite(latlng[0]) || !isFinite(latlng[1])) continue;

                    let marker = markersById.get(String(id));
                    if (!marker) {
                        marker = L.marker(latlng, { icon: redIcon() }).addTo(map);
                        markersById.set(String(id), marker);
                    } else {
                        marker.setLatLng(latlng);
                    }

                    const safeId = String(id).replace(/'/g, "\\'");
                    marker.bindPopup(`
                        <strong>${sjn || '-'}</strong><br>
                        ${item.send_to || '-'}<br>
                        ${item.project || '-'}<br>
                        <small>${loc.timestamp || ''}</small><br>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="goDetail('${safeId}')">
                                Lihat Detail
                            </button>
                        </div>
                    `, { autoClose: true, closeOnClick: true });
                }

                for (const [id, marker] of markersById.entries()) {
                    if (!activeSet.has(id)) {
                        map.removeLayer(marker);
                        markersById.delete(id);
                    }
                }
            } catch (e) {
                console.warn('fetchActiveLast failed:', e);
            }
        }

        function fitToActiveMarkers() {
            const latlngs = [];
            for (const marker of markersById.values()) {
                latlngs.push(marker.getLatLng());
            }
            if (latlngs.length) {
                map.fitBounds(L.latLngBounds(latlngs), { padding: [40, 40] });
            }
        }


            // Initial load + polling
            fetchActiveLast().then(() => {
            // lakukan fit sekali saja saat awal
            fitToActiveMarkers();
        });

        setInterval(fetchActiveLast, 15000);

    </script>
@endpush
