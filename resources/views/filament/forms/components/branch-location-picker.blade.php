@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        window.initBranchLocationPicker = (element, initialLatitude, initialLongitude, statePath, wire) => {
            if (element.dataset.mapInitialized) {
                return;
            }

            element.dataset.mapInitialized = 'true';

            const hasLocation = initialLatitude !== null && initialLongitude !== null &&
                initialLatitude !== '' && initialLongitude !== '';
            const latitude = hasLocation ? Number(initialLatitude) : -6.2;
            const longitude = hasLocation ? Number(initialLongitude) : 106.82;
            const map = L.map(element).setView([latitude, longitude], hasLocation ? 15 : 12);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);

            let marker = hasLocation ? L.marker([latitude, longitude]).addTo(map) : null;

            map.on('click', (event) => {
                const selectedLatitude = Number(event.latlng.lat.toFixed(7));
                const selectedLongitude = Number(event.latlng.lng.toFixed(7));

                if (marker) {
                    marker.setLatLng(event.latlng);
                } else {
                    marker = L.marker(event.latlng).addTo(map);
                }

                wire.$set(`${statePath}.latitude`, selectedLatitude, false);
                wire.$set(`${statePath}.longitude`, selectedLongitude, false);
            });
        };
    </script>
@endonce

<div>
    <p class="mb-2 text-sm text-gray-600">Klik pada peta untuk memilih lokasi cabang.</p>
    <div
        id="branch-location-map"
        class="w-full rounded-lg border border-gray-300"
        style="height: 380px;"
        wire:ignore
        x-init="window.initBranchLocationPicker($el, @js($get('latitude')), @js($get('longitude')), @js($schemaComponent->getContainer()->getStatePath()), $wire)"
    ></div>
</div>