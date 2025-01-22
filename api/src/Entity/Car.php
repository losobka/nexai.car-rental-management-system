<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\RecordedGeolocation as CarUpdateCurrentPositionDto;
use App\Embeddable\Address;
use App\Embeddable\RecordedGeolocation;
use App\Enum\CarBrand;
use App\Repository\CarRepository;
use App\State\CarCurrentPositionStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['car:item:read']]),
        new GetCollection(
            normalizationContext: ['groups' => ['car:collection:read']]
        ),
        new Post(
            normalizationContext: ['groups' => ['car:create:read']],
            denormalizationContext: ['groups' => ['car:create:write']]
        ),
        new Post(
            uriTemplate: '/cars/{id}/positions',
            status: 204,
            denormalizationContext: ['groups' => ['car:current_position:write']],
            input: CarUpdateCurrentPositionDto::class,
            name: 'api_cars_id_post_positions',
            processor: CarCurrentPositionStateProcessor::class
        ),
        new Patch(
            normalizationContext: ['groups' => ['car:update:read']],
            denormalizationContext: ['groups' => ['car:update:write']]
        ),
        new Delete
    ],
)]
#[UniqueEntity(fields: ['vin'])]
final class Car
{
    public const REGISTRATION_REGEX = '@^[A-Z]{1}[A-Z\d]{2,11}$@';
    public const VIN_REGEX = '@^[A-Z]{1}[A-Z\d]{16}$@';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:update:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: CarBrand::class)]
    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:create:write', 'car:update:read', 'car:update:write'])]
    private ?CarBrand $brand;

    #[ORM\Column(length: 12, unique: true)]
    #[Assert\Regex(pattern: self::REGISTRATION_REGEX, message: 'Invalid registration')]
    #[ApiProperty(openapiContext: [
        'example' => 'ZK3666'
    ])]
    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:create:write', 'car:update:read', 'car:update:write'])]
    private string $registration = '';

    #[ORM\Column(length: 17, unique: true)]
    #[Assert\Regex(pattern: self::VIN_REGEX, message: 'Invalid VIN')]
    #[ApiProperty(
        openapiContext: [
            'example' => 'K9ITO0C2W2BR1N12M'
        ]
    )]
    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:create:write', 'car:update:read'])]
    private string $vin = '';

    #[ORM\Column]
    private bool $rented = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email]
    #[ApiProperty(openapiContext: [
        'description' => 'Please type only when the car is rented'
    ])]
    private ?string $customerEmail = null;

    #[ORM\Embedded(class: Address::class, columnPrefix: 'billing_')]
    #[ApiProperty]
    private Address $billingAddress;

    #[ORM\Embedded(class: RecordedGeolocation::class)]
    private RecordedGeolocation $currentPosition;

    public function __construct()
    {
        $this->billingAddress = new Address;
        $this->currentPosition = new RecordedGeolocation;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): CarBrand
    {
        return $this->brand;
    }

    public function setBrand(CarBrand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getRegistration(): string
    {
        return $this->registration;
    }

    public function setRegistration(string $registration): static
    {
        $this->registration = $registration;

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

    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:create:write', 'car:update:read', 'car:update:write'])]
    public function getBillingAddress(): ?Address
    {
        if (
            empty($this->billingAddress->getStreet())
                || empty($this->billingAddress->getStreet())
                || empty($this->billingAddress->getPostalCode())
        )
                return null;

        if (false === $this->isRented())
            return null;

        return $this->billingAddress;
    }

    public function setBillingAddress(Address $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }
    #[Groups(['cat:item:read', 'car:collection:read', 'car:create:read', 'car:update:current_position:write', 'car:update:read'])]
    public function getCurrentPosition(): ?RecordedGeolocation
    {
        if (
            null === $this->currentPosition->latitude
                || null === $this->currentPosition->longitude
        )
            return null;

        return $this->currentPosition;
    }

    public function setCurrentPosition(RecordedGeolocation $currentPosition): static
    {
        $this->currentPosition = $currentPosition;

        return $this;
    }
}
