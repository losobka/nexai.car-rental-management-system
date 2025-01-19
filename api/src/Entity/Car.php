<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    public const AVAILABLE_BRANDS = [
        'Audi',
        'BMW',
        'Chevrolet',
        'Ferrari',
        'Ford',
        'Honda',
        'Hyundai',
        'Jeep',
        'Kia',
        'Lamborghini',
        'Lexus',
        'Mazda',
        'Mercedes-Benz',
        'Nissan',
        'Porsche',
        'Subaru',
        'Tesla',
        'Toyota',
        'Volkswagen',
        'Volvo'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $brand = '';

    #[ORM\Column(length: 12, unique: true)]
    private string $registrationNumber = '';

    #[ORM\Column(length: 17, unique: true)]
    private string $vin = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customerEmail = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $customerAddress = null;

    #[ORM\Column]
    private bool $rented = false;

    #[ORM\Column(precision: 5)]
    private float $currentLat = 0;

    #[ORM\Column(precision: 5)]
    private float $currentLng = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): static
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    public function getVin(): string
    {
        return $this->vin;
    }

    public function setVin(string $vin): static
    {
        $this->vin = $vin;

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(?string $customerEmail): static
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress(?string $customerAddress): static
    {
        $this->customerAddress = $customerAddress;

        return $this;
    }

    public function isRented(): bool
    {
        return $this->rented;
    }

    public function setRented(bool $isRented): static
    {
        $this->rented = $isRented;

        return $this;
    }

    public function getCurrentLat(): float
    {
        return $this->currentLat;
    }

    public function setCurrentLat(float $currentLat): static
    {
        $this->currentLat = $currentLat;

        return $this;
    }

    public function getCurrentLng(): float
    {
        return $this->currentLng;
    }

    public function setCurrentLng(float $currentLng): static
    {
        $this->currentLng = $currentLng;

        return $this;
    }
}
