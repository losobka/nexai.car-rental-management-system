<?php

declare(strict_types=1);

namespace App\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Geolocation
{
    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: -90, max: 90)]
    #[Groups(['car:item:read', 'car:collection:read', 'car:update:current_position:read', 'car:update:current_position:write', 'car:update:read'])]
    public ?float $latitude;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: -180, max: 180)]
    #[Groups(['car:item:read', 'car:collection:read', 'car:update:current_position:read', 'car:update:current_position:write', 'car:update:read'])]
    public ?float $longitude;

    public function __construct(?float $latitude = null, ?float $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}