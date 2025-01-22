<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

class Geolocation
{
    public function __construct(
        #[Groups(['car:current_position:write'])]
        public readonly float $latitude,
        #[Groups(['car:current_position:write'])]
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