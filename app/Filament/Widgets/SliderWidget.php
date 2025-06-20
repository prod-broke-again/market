<?php

namespace App\Filament\Widgets;

use App\Models\Slider;
use Filament\Widgets\Widget;

class SliderWidget extends Widget
{
    protected static string $view = 'filament.widgets.slider-widget';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $sliders = Slider::orderByDesc('created_at')->take(5)->get();
        return [
            'sliders' => $sliders,
        ];
    }
} 