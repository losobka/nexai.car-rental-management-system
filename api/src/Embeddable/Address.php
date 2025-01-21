<?php

namespace App\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Address
{
    #[ORM\Column(nullable: true)]
    private ?string $street;

    #[ORM\Column(nullable: true)]
    private ?string $postalCode;

    #[ORM\Column(nullable: true)]
    private ?string $city;

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        if (0 === mb_strlen($city))
            $city = null;

        $this->city = $city;
    }

    public function setPostalCode(?string $postalCode): void
    {
        if (0 === mb_strlen($postalCode))
            $postalCode = null;

        $this->postalCode = $postalCode;
    }

    public function setStreet(?string $street): void
    {
        if (0 === mb_strlen($street))
            $street = null;

        $this->street = $street;
    }
}