@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('livewire:load', () => {
            new Swiper('.swiper', {
                loop: true,
                autoplay: { delay: 4000 },
                pagination: { el: '.swiper-pagination', clickable: true },
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            });
        });
    </script>
@endpush

@if($sliders->count())
<div class="w-full h-96 border border-gray-300 rounded-lg overflow-hidden">
    <div class="swiper" style="width: 100%;">
        <div class="swiper-wrapper">
            @foreach($sliders as $slide)
                <div class="swiper-slide">
                    <a href="{{ $slide->url }}" target="_blank">
                        <img src="{{ asset('storage/' . $slide->image) }}"
                             alt="Слайд"
                             style="width: 100%; height: 350px; object-fit: cover; border-radius: 20px;">
                    </a>
                </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</div>
@else
    <div class="filament-box p-4 text-center text-gray-500">
        Нет слайдов для отображения.
    </div>
@endif 