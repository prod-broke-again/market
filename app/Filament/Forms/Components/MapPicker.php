<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Get;
use Filament\Forms\Set;

class MapPicker extends Field
{
    protected string $view = 'filament.forms.components.map-picker';

    protected string $mapType;
    protected float $defaultLatitude;
    protected float $defaultLongitude;
    protected int $zoom;
    protected string $height;
    protected string $width;

    public function mapType(string $type): static
    {
        $this->mapType = $type;
        return $this;
    }

    public function defaultCoordinates(float $latitude, float $longitude): static
    {
        $this->defaultLatitude = $latitude;
        $this->defaultLongitude = $longitude;
        return $this;
    }

    public function zoom(int $zoom): static
    {
        $this->zoom = $zoom;
        return $this;
    }

    public function height(string $height): static
    {
        $this->height = $height;
        return $this;
    }

    public function width(string $width): static
    {
        $this->width = $width;
        return $this;
    }

    public function getMapType(): string
    {
        return $this->mapType ?? config('maps.default_provider', 'google');
    }

    public function getDefaultLatitude(): float
    {
        return $this->defaultLatitude ?? config('maps.defaults.latitude', 55.7558);
    }

    public function getDefaultLongitude(): float
    {
        return $this->defaultLongitude ?? config('maps.defaults.longitude', 37.6176);
    }

    public function getZoom(): int
    {
        return $this->zoom ?? config('maps.defaults.zoom', 12);
    }

    public function getHeight(): string
    {
        return $this->height ?? config('maps.defaults.height', '400px');
    }

    public function getWidth(): string
    {
        return $this->width ?? config('maps.defaults.width', '100%');
    }

    public function getApiKey(): ?string
    {
        $provider = config("maps.providers.{$this->getMapType()}");
        return $provider['api_key'] ?? null;
    }

    public function getProviderConfig(): array
    {
        return config("maps.providers.{$this->getMapType()}", []);
    }

    public function getFeatures(): array
    {
        return config('maps.features', []);
    }
} 