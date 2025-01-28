<?php

declare(strict_types=1);

namespace App\Embeddable;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Embeddable]
final class RecordedGeolocation extends Geolocation
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['car:item:read', 'car:collection:read', 'car:update:current_position:read', 'car:update:current_position:write', 'car:update:read'])]
    public ?DateTimeInterface $recordedAt;

    public function __construct(?float $latitude = null, ?float $longitude = null)
    {
        parent::__construct($latitude, $longitude);

        if (
            null !== $latitude
                && null !== $longitude
        )
            $this->recordedAt = new DateTime;
    }
}