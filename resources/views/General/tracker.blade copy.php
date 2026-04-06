<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
        Manajemen Pengiriman | Rekatrack
    </title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body
    x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode'));
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}"
>

    <div
         x-data="{ show: true, type: '', message: '' }"
    x-init="
        console.log('Alert init, show:', show);
        window.addEventListener('alert', e => {
            console.log('Alert event received:', e.detail);
            type = e.detail.type;
            message = e.detail.message;
            show = true;
            console.log('Show set to:', show);
            setTimeout(() => show = true, 5000);
        });
    "
        x-show="show"
        :class="{
            'border-error-500 bg-error-50': type === 'error',
            'border-success-500 bg-success-50': type === 'success',
            'border-info-500 bg-info-50': type === 'info',
            'border-warning-500 bg-warning-50': type === 'warning'
        }"
        class="fixed top-5 right-5 rounded-xl border p-4 transition duration-500 z-50 w-96"
    >
        <div class="flex items-start gap-3">
            <div class="text-sm font-semibold text-gray-800" x-text="type.toUpperCase() + ' MESSAGE'"></div>
            <div class="text-sm text-gray-500" x-text="message"></div>
        </div>
    </div>


    @include('partials.preloader')
    <div class="flex h-screen overflow-hidden">
        @include('Template.sidebar')
        <div
            class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto"
        >
            @include('partials.overlay')
            @include('Template.header')
            <main class="flex flex-col h-screen">
                <div class="bg-white z-10 shadow-md">
                <div class="bg-white dark:bg-black bg-opacity-70 p-2">
                    <div class="flex space-x-2">
                    <input
                        type="text"
                        id="search"
                        placeholder="Cari Berdasarkan Surat Jalan..."
                        class="flex-1 px-4 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-900 dark:text-gray-200 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    />
                    <button
                        onclick="searchTracking()"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md"
                    >
                        Cari
                    </button>
                    </div>
                </div>
                </div>

                <div class="flex-1 relative">
                    <div id="map" class="absolute top-0 left-0 w-full h-full"></div>
                </div>
            </main>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        var center = [-7.61617286255246, 111.52143728913316];

        var map = L.map("map").setView(center, 10); 
    
        L.tileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 18,
        }).addTo(map);

        L.marker(center).addTo(map);
    </script>

    <script>
       function showCustomAlert(type, message) {
            alert(type + '\n' + message);
        }


        function searchTracking() {
            var searchQuery = document.getElementById("search").value;

            if (searchQuery) {
                fetch(`/search-travel-document?no_travel_document=${searchQuery}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateMapWithLocations(data.locations);
                        } else {
                            showCustomAlert('error', 'Data tidak ditemukan');
                        }
                    })
                    .catch(error => {
                        console.error("Terjadi kesalahan:", error);
                        showCustomAlert('error', 'Gagal mengambil data');
                    });
            } else {
                showCustomAlert('warning', 'Masukkan nomor surat jalan');
            }
        }

        function updateMapWithLocations(locations) {
            map.eachLayer(function (layer) {
                if (layer instanceof L.Marker || layer instanceof L.Polyline) {
                    map.removeLayer(layer);
                }
            });

            const latLngs = locations.map(location => [location.latitude, location.longitude]);

            if (latLngs.length > 1) {
                var start = latLngs[0];
                var end = latLngs[latLngs.length - 1];
                
                var apiKey = '5b3ce3597851110001cf6248b4c0aaa51d204cea888ada05975d8638'; // API

                function getRoute(start, end) {
                    var url = `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}&start=${start[1]},${start[0]}&end=${end[1]},${end[0]}`;

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            var route = data.features[0].geometry.coordinates;
                            var latlngs = route.map(function(coord) {
                                return [coord[1], coord[0]];  
                            });

                            L.polyline(latlngs, { color: 'blue', weight: 4, opacity: 0.7 }).addTo(map);
                            map.fitBounds(L.latLngBounds(latlngs));
                        })
                        .catch(error => console.error('Error fetching route:', error));
                }

                getRoute(start, end);
            }

            // locations.forEach(location => {
            //     const latLng = [location.latitude, location.longitude];
            //     L.marker(latLng).addTo(map).bindPopup(`Lokasi: ${location.latitude}, ${location.longitude}`);
            // });

            // if (latLngs.length === 1) {
            //     L.marker(latLngs[0]).addTo(map);
            // }

            // if (latLngs.length > 1) {
            //     map.fitBounds(L.latLngBounds(latLngs));
            // }

            const lastLocation = locations[locations.length - 1];
            const lastLatLng = [lastLocation.latitude, lastLocation.longitude];
            L.marker(lastLatLng).addTo(map).bindPopup(`Lokasi: ${lastLocation.latitude}, ${lastLocation.longitude}`);

            map.setView(lastLatLng, 13);

        }
    </script>

</body>
</html>
