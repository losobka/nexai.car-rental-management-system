<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

class Geolocation
{
    public function __construct(
        #[Groups(['car:update:current_position:write', 'car:update:current_position:read'])]
        public readonly float $latitude,
        #[Groups(['car:update:current_position:write', 'car:update:current_position:read'])]
        public readonly float $longitude
    ) {
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}