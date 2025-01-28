<?php

namespace App\Dto;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\Groups;

class RecordedGeolocation extends Geolocation
{
    #[Groups(['car:update:current_position:write', 'car:update:current_position:read'])]
    public readonly DateTimeInterface $recordedAt;

    public function __construct(
        float $longitude,
        float $latitude,
        ?DateTimeInterface $recordedAt = null
    ) {
        parent::__construct($longitude, $latitude);

        if (null === $recordedAt) {
            $recordedAt = new DateTime();
        }

        $this->recordedAt = $recordedAt;
    }

    public function getRecordedAt(): DateTimeInterface
    {
        return $this->recordedAt;
    }
}