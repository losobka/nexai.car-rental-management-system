<?php

declare(strict_types=1);

namespace App\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Embeddable]
final class Address
{
    #[ORM\Column(nullable: true)]
    private ?string $street = null;

    #[ORM\Column(nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(nullable: true)]
    private ?string $city = null;

    #[Groups(['car:item:read', 'car:collection:read', 'car:create:read', 'car:update:read', 'rental:item:read', 'rental:collection:read', 'rental:create:read'])]
    public function getStreet(): ?string
    {
        return $this->street;
    }
    #[Groups(['car:item:read', 'car:collection:read', 'car:create:read', 'car:update:read', 'rental:item:read', 'rental:collection:read', 'rental:create:read'])]
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }
    #[Groups(['car:item:read', 'car:collection:read', 'car:create:read', 'car:update:read', 'rental:item:read', 'rental:collection:read', 'rental:create:read'])]
    public function getCity(): ?string
    {
        return $this->city;
    }

    #[Groups(['rental:create:write'])]
    public function setCity(?string $city): void
    {
        if (0 === mb_strlen((string) $city))
            $city = null;

        $this->city = $city;
    }

    #[Groups(['rental:create:write'])]
    public function setPostalCode(?string $postalCode): void
    {
        if (0 === mb_strlen((string) $postalCode))
            $postalCode = null;

        $this->postalCode = $postalCode;
    }

    #[Groups(['rental:create:write'])]
    public function setStreet(?string $street): void
    {
        if (0 === mb_strlen((string) $street))
            $street = null;

        $this->street = $street;
    }
}