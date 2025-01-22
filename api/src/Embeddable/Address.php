<?php

declare(strict_types=1);

namespace App\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Embeddable]
final class Address
{
    #[ORM\Column(nullable: true)]
    private ?string $street;

    #[ORM\Column(nullable: true)]
    private ?string $postalCode;

    #[ORM\Column(nullable: true)]
    private ?string $city;

    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:update:read'])]
    public function getStreet(): ?string
    {
        return $this->street;
    }
    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:update:read'])]
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }
    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:update:read'])]
    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        if (0 === mb_strlen((string) $city))
            $city = null;

        $this->city = $city;
    }

    public function setPostalCode(?string $postalCode): void
    {
        if (0 === mb_strlen((string) $postalCode))
            $postalCode = null;

        $this->postalCode = $postalCode;
    }

    public function setStreet(?string $street): void
    {
        if (0 === mb_strlen((string) $street))
            $street = null;

        $this->street = $street;
    }
}