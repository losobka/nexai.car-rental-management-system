<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class LatitudeGenerator
{
    public function __construct(
        #[Autowire('%app.geolocation_precision%')]
        private float $precision
    ) {
    }

    public function generate(): float
    {
        $latitudes = range(-90, 90, $this->precision);

        return round($latitudes[array_rand($latitudes)], mb_strlen((string) $this->precision) - 2);
    }
}