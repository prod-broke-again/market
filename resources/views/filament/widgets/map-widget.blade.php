<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold tracking-tight">
                Карта адресов
            </h2>
            <span class="text-sm text-gray-500">
                {{ $addresses->count() }} адресов на карте
            </span>
        </div>

        <div 
            x-data="{
                addresses: {{ Js::from($addresses) }},
                apiKey: '{{ $apiKey }}',
                map: null,
                markers: [],
                init() {
                    this.initMap();
                    // Реинициализация после обновления Livewire
                    Livewire.hook('commit', () => {
                        this.initMap();
                    });
                },
                initMap() {
                    if (typeof google === 'undefined') {
                        this.loadGoogleMapsScript();
                        return;
                    }
                    this.createMap();
                },
                loadGoogleMapsScript() {
                    if (document.getElementById('google-maps-script')) {
                        // Если скрипт уже есть, просто ждем загрузки
                        if (typeof google !== 'undefined') {
                            this.createMap();
                        }
                        return;
                    }
                    const script = document.createElement('script');
                    script.id = 'google-maps-script';
                    script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&libraries=places`;
                    script.async = true;
                    script.defer = true;
                    script.onload = () => this.createMap();
                    document.head.appendChild(script);
                },
                createMap() {
                    const mapElement = this.$refs.mapContainer;
                    let center = { lat: 55.7558, lng: 37.6176 };
                    if (this.addresses.length > 0) {
                        const bounds = new google.maps.LatLngBounds();
                        this.addresses.forEach(address => {
                            const lat = Number(address.latitude);
                            const lng = Number(address.longitude);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                bounds.extend({ lat, lng });
                            }
                        });
                        center = bounds.getCenter();
                    }
                    this.map = new google.maps.Map(mapElement, {
                        center: center,
                        zoom: this.addresses.length > 0 ? 10 : 8,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        streetViewControl: false,
                        fullscreenControl: false,
                    });
                    this.markers = [];
                    this.addresses.forEach(address => {
                        const lat = Number(address.latitude);
                        const lng = Number(address.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            const marker = new google.maps.Marker({
                                position: { lat, lng },
                                map: this.map,
                                title: address.address,
                                icon: {
                                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                                    scaledSize: new google.maps.Size(32, 32)
                                }
                            });
                            const infoWindow = new google.maps.InfoWindow({
                                content: `
                                    <div class='p-2'>
                                        <h3 class='font-bold text-sm'>${address.address}</h3>
                                        ${address.user ? `<p class='text-xs text-gray-600'>Пользователь: ${address.user}</p>` : ''}
                                        ${address.product ? `<p class='text-xs text-gray-600'>Товар: ${address.product}</p>` : ''}
                                        ${address.shop ? `<p class='text-xs text-gray-600'>Магазин: ${address.shop}</p>` : ''}
                                    </div>
                                `
                            });
                            marker.addListener('click', () => {
                                infoWindow.open(this.map, marker);
                            });
                            this.markers.push(marker);
                        }
                    });
                    if (this.addresses.length > 0) {
                        const bounds = new google.maps.LatLngBounds();
                        this.markers.forEach(marker => {
                            bounds.extend(marker.getPosition());
                        });
                        this.map.fitBounds(bounds);
                    }
                }
            }"
            x-init="init()"
            class="mt-4"
        >
            <div 
                x-ref="mapContainer" 
                class="w-full h-96 border border-gray-300 rounded-lg overflow-hidden"
            ></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget> 