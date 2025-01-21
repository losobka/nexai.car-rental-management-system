<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class LongitudeGenerator
{
    public function __construct(
        #[Autowire('%app.geolocation_precision%')]
        private float $precision
    ) {
    }

    public function generate(): float
    {
        $longitudes = range(-180, 180, $this->precision);

        return round($longitudes[array_rand($longitudes)], mb_strlen((string) $this->precision) - 2);
    }
}