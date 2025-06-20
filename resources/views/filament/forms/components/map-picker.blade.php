<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $mapId = 'map-' . uniqid();
        $apiKey = $getApiKey();
        $provider = $getMapType();
        $providerConfig = $getProviderConfig();
        $features = $getFeatures();

        $statePath = $getStatePath();
        $latitudePath = 'data.latitude';
        $longitudePath = 'data.longitude';
        $addressPath = 'data.address';
    @endphp

    <div
        x-data="mapPicker({
            state: $wire.entangle('{{ $statePath }}').live,
            addressState: $wire.entangle('{{ $addressPath }}').live,
            latitudeState: $wire.entangle('{{ $latitudePath }}').live,
            longitudeState: $wire.entangle('{{ $longitudePath }}').live,
            provider: '{{ $provider }}',
            apiKey: '{{ $apiKey }}',
            providerConfig: {{ json_encode($providerConfig) }},
            features: {{ json_encode($features) }},
            mapId: '{{ $mapId }}',
            defaultLatitude: {{ $getDefaultLatitude() }},
            defaultLongitude: {{ $getDefaultLongitude() }},
            zoom: {{ $getZoom() }}
        })"
        x-init="init()"
        wire:ignore
        class="space-y-4"
    >
        <!-- Поля для отладки и отображения -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <input
                type="text"
                x-model="address"
                placeholder="Адрес"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
            <input
                type="text"
                x-model="latitude"
                placeholder="Широта"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
            <input
                type="text"
                x-model="longitude"
                placeholder="Долгота"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
        </div>

        <!-- Карта -->
        <div
            id="{{ $mapId }}"
            style="height: {{ $getHeight() }}; width: {{ $getWidth() }};"
            class="rounded-md"
        ></div>
        
        <!-- Кнопки управления -->
        <div class="flex items-center gap-x-3">
            @if($features['current_location'])
                <x-filament::button
                    type="button"
                    @click="getCurrentLocation()"
                    icon="heroicon-s-map-pin"
                >
                    Моё местоположение
                </x-filament::button>
            @endif

            <x-filament::button
                type="button"
                @click="clearMap()"
                color="gray"
                icon="heroicon-s-x-circle"
            >
                Очистить
            </x-filament::button>
        </div>
    </div>
</x-dynamic-component>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mapPicker', (config) => ({
            state: config.state,
            addressState: config.addressState,
            latitudeState: config.latitudeState,
            longitudeState: config.longitudeState,
            
            map: null,
            marker: null,
            geocoder: null,
            
            address: '',
            latitude: '',
            longitude: '',

            init() {
                this.address = this.addressState || '';
                this.latitude = this.latitudeState || config.defaultLatitude;
                this.longitude = this.longitudeState || config.defaultLongitude;
                
                if (config.provider === 'google') {
                    this.initGoogleMap();
                } else if (config.provider === 'yandex') {
                    this.initYandexMap();
                }

                this.$watch('addressState', (value) => {
                    if (this.address !== value) {
                        this.address = value;
                        if (config.features.geocoding) this.geocodeAddress(value);
                    }
                });
            },

            // Инициализация Google Maps
            initGoogleMap() {
                if (typeof google === 'undefined') {
                    const script = document.createElement('script');
                    script.src = `https://maps.googleapis.com/maps/api/js?key=${config.apiKey}&libraries=places,geocoding&language=ru&callback=initMapPicker`;
                    script.async = true;
                    script.defer = true;
                    window.initMapPicker = () => this.createGoogleMap();
                    document.head.appendChild(script);
                } else {
                    this.createGoogleMap();
                }
            },
            
            createGoogleMap() {
                this.map = new google.maps.Map(document.getElementById(config.mapId), {
                    center: { lat: parseFloat(this.latitude), lng: parseFloat(this.longitude) },
                    zoom: config.zoom,
                });
                this.geocoder = new google.maps.Geocoder();
                
                this.marker = new google.maps.Marker({
                    position: { lat: parseFloat(this.latitude), lng: parseFloat(this.longitude) },
                    map: this.map,
                    draggable: config.features.draggable_marker,
                });

                if (config.features.click_to_place) {
                    this.map.addListener('click', (e) => {
                        this.updateMarkerPosition(e.latLng.lat(), e.latLng.lng());
                        if (config.features.reverse_geocoding) this.reverseGeocode(e.latLng);
                    });
                }
                
                this.marker.addListener('dragend', (e) => {
                    this.updateMarkerPosition(e.latLng.lat(), e.latLng.lng());
                    if (config.features.reverse_geocoding) this.reverseGeocode(e.latLng);
                });

                this.setupAutocomplete('address');
            },

            // Инициализация Yandex Maps
            initYandexMap() {
                if (typeof ymaps === 'undefined') {
                    const script = document.createElement('script');
                    script.src = `https://api-maps.yandex.ru/${config.providerConfig.version || '2.1'}/?apikey=${config.apiKey}&lang=${config.providerConfig.language || 'ru_RU'}`;
                    script.onload = () => ymaps.ready(() => this.createYandexMap());
                    document.head.appendChild(script);
                } else {
                    ymaps.ready(() => this.createYandexMap());
                }
            },
            
            createYandexMap() {
                this.map = new ymaps.Map(config.mapId, {
                    center: [this.latitude, this.longitude],
                    zoom: config.zoom,
                });

                this.marker = new ymaps.Placemark([this.latitude, this.longitude], {}, {
                    preset: 'islands#redDotIcon',
                    draggable: config.features.draggable_marker,
                });

                this.map.geoObjects.add(this.marker);
                
                if (config.features.click_to_place) {
                    this.map.events.add('click', (e) => {
                        const coords = e.get('coords');
                        this.updateMarkerPosition(coords[0], coords[1]);
                        if (config.features.reverse_geocoding) this.reverseGeocode(coords);
                    });
                }
                
                this.marker.events.add('dragend', () => {
                    const coords = this.marker.geometry.getCoordinates();
                    this.updateMarkerPosition(coords[0], coords[1]);
                    if (config.features.reverse_geocoding) this.reverseGeocode(coords);
                });

                this.setupAutocomplete('address');
            },

            // Общие функции
            updateMarkerPosition(lat, lng) {
                this.latitude = lat;
                this.longitude = lng;
                this.updateState();

                if (config.provider === 'google') {
                    this.marker.setPosition({ lat, lng });
                } else if (config.provider === 'yandex') {
                    this.marker.geometry.setCoordinates([lat, lng]);
                }
            },
            
            reverseGeocode(coords) {
                if (config.provider === 'google') {
                    this.geocoder.geocode({ 'location': coords }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            this.address = results[0].formatted_address;
                            this.updateState();
                        }
                    });
                } else if (config.provider === 'yandex') {
                    ymaps.geocode(coords).then((res) => {
                        const firstGeoObject = res.geoObjects.get(0);
                        if (firstGeoObject) {
                            this.address = firstGeoObject.getAddressLine();
                            this.updateState();
                        }
                    });
                }
            },

            geocodeAddress(address) {
                if (!address) return;
                
                if (config.provider === 'google') {
                    this.geocoder.geocode({ 'address': address }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            const location = results[0].geometry.location;
                            this.updateMarkerPosition(location.lat(), location.lng());
                            this.map.setCenter(location);
                        }
                    });
                } else if (config.provider === 'yandex') {
                    ymaps.geocode(address).then((res) => {
                        const firstGeoObject = res.geoObjects.get(0);
                        if (firstGeoObject) {
                            const coords = firstGeoObject.geometry.getCoordinates();
                            this.updateMarkerPosition(coords[0], coords[1]);
                            this.map.setCenter(coords);
                        }
                    });
                }
            },
            
            setupAutocomplete(elementId) {
                const input = this.$el.querySelector(`[x-model='${elementId}']`);
                if (!input) return;
                
                if (config.provider === 'google' && config.features.autocomplete) {
                    const autocomplete = new google.maps.places.Autocomplete(input);
                    autocomplete.addListener('place_changed', () => {
                        const place = autocomplete.getPlace();
                        if (place.geometry) {
                            const location = place.geometry.location;
                            this.address = place.formatted_address;
                            this.updateMarkerPosition(location.lat(), location.lng());
                            this.map.setCenter(location);
                        } else {
                            this.geocodeAddress(input.value);
                        }
                    });
                }
                // Для Yandex можно добавить саджест
            },
            
            getCurrentLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        const coords = config.provider === 'google'
                            ? { lat: position.coords.latitude, lng: position.coords.longitude }
                            : [position.coords.latitude, position.coords.longitude];
                        
                        this.updateMarkerPosition(position.coords.latitude, position.coords.longitude);
                        this.reverseGeocode(coords);
                        this.map.setCenter(coords);
                    });
                }
            },

            clearMap() {
                this.address = '';
                this.latitude = config.defaultLatitude;
                this.longitude = config.defaultLongitude;
                this.updateMarkerPosition(this.latitude, this.longitude);
                this.map.setCenter(config.provider === 'google' ? { lat: this.latitude, lng: this.longitude } : [this.latitude, this.longitude]);
                this.updateState();
            },

            updateState() {
                this.state = {
                    latitude: this.latitude,
                    longitude: this.longitude,
                    address: this.address,
                };
                
                // Обновляем внешние поля формы
                this.latitudeState = this.latitude;
                this.longitudeState = this.longitude;
                this.addressState = this.address;
            }
        }));
    });
</script>
@endpush 