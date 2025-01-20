<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(paginationEnabled: false),
        new Post(),
        new Put(),
        new Delete()
    ]
)]
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
    #[ApiProperty(openapiContext: [
        'example' => 1
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Assert\Choice(choices: self::AVAILABLE_BRANDS)]
    private string $brand = '';

    #[ORM\Column(length: 12, unique: true)]
    #[Assert\Regex(pattern: '@^[A-Z]{1}[A-Z\d]{2,11}$@', message: 'Invalid registration number')]
    #[ApiProperty(openapiContext: [
        'example' => 'ZK3666'
    ])]
    private string $registrationNumber = '';

    #[ORM\Column(length: 17, unique: true)]
    #[Assert\Regex(pattern: '@^[A-Z\d]{17}$@', message: 'Invalid VIN')]
    #[ApiProperty(openapiContext: [
        'example' => 'K9ITO0C2W2BR1N12M'
    ])]
    private string $vin = '';

    #[ORM\Column]
    private bool $rented = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email]
    #[ApiProperty(openapiContext: [
        'description' => 'Please type only when the car is rented'
    ])]
    private ?string $customerEmail = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[ApiProperty(openapiContext: [
        'description' => 'Please type only when the car is rented',
        'example' => 'ul. Kwiatowa 1/10B, 03-528 Warszawa'
    ])]
    private ?string $customerAddress = null;

    #[ORM\Column(precision: 5)]
    #[ApiProperty(readable: true, writable: false)]
    #[Assert\Range(min: -90, max: 90)]
    private float $latitude = 0;

    #[ORM\Column(precision: 5)]
    #[ApiProperty(readable: true, writable: false)]
    #[Assert\Range(min: -180, max: 180)]
    private float $longitude = 0;

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

    public function isRented(): bool
    {
        return $this->rented;
    }

    public function setRented(bool $isRented): static
    {
        $this->rented = $isRented;

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

    public function getLatitude(): float
    {
        return round($this->latitude, 5);
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return round($this->longitude, 5);
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    #[Assert\Callback]
    public function validateIfCustomerEmailIsEmptyWhenCarIsNotRented(ExecutionContextInterface $context, mixed $payload): void
    {
        if ($this->isRented() || empty($this->getCustomerEmail()))
            return;

        $context->buildViolation('Customer email address should be empty')
            ->atPath('customerEmail')
            ->addViolation();
    }

    #[Assert\Callback]
    public function validateIfCustomerAddressIsEmptyWhenCarIsNotRented(ExecutionContextInterface $context, mixed  $payload): void
    {
        if ($this->isRented() || empty($this->getCustomerAddress()))
            return;

        $context->buildViolation('Customer address should be empty')
            ->atPath('customerAddress')
            ->addViolation();
    }

    #[Assert\Callback]
    public function validateIfCustomerEmailIsProvidedWhenCarIsRented(ExecutionContextInterface $context, mixed  $payload): void
    {
        if (false === $this->isRented() || false === empty($this->getCustomerEmail()))
            return;

        $context->buildViolation('Customer email address is required')
            ->atPath('customerEmail')
            ->addViolation();
    }

    #[Assert\Callback]
    public function validateIfCustomerAddressIsProvidedWhenCarIsRented(ExecutionContextInterface $context, mixed $payload): void
    {
        if (false === $this->isRented() || false === empty($this->getCustomerAddress()))
            return;

        $context->buildViolation('Customer address is required')
            ->atPath('customerAddress')
            ->addViolation();
    }

    #[Assert\Callback]
    public function validateCustomerAddress(ExecutionContextInterface $context, mixed $payload): void
    {
        if (null === $this->getCustomerAddress())
            return;

        if (false === preg_match(pattern: '@^ul\. [:alnum]{3,32}, \d{2}-\d{4} [:alnum]{3,18}$@u', subject: $this->getCustomerAddress()))
            $context->buildViolation('Invalid customer address format')
                ->atPath('customerAddress')
                ->addViolation();
    }
}
