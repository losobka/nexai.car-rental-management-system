<?php

namespace App\Dto;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\Groups;

class RecordedGeolocation extends Geolocation
{
    #[Groups(['car:current_position:write'])]
    public readonly DateTimeInterface $recordedAt;

    public function __construct(
        float $longitude,
        float $latitude,
        string $recordedAt = 'NOW'
    ) {
        parent::__construct($longitude, $latitude);

        $this->recordedAt = new DateTime($recordedAt);
    }

    public function getRecordedAt(): DateTimeInterface
    {
        return $this->recordedAt;
    }
}