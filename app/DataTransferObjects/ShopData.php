<?php

namespace App\DataTransferObjects;

readonly class ShopData
{
    public function __construct(
        public string $name,
        public string $description,
        public int $userId,
        public ?string $phone = null,
    ) {}
} 