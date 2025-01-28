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
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\RecordedGeolocation as CarUpdateCurrentPositionDto;
use App\Embeddable\RecordedGeolocation;
use App\Enum\CarBrand;
use App\Repository\CarRepository;
use App\State\CarCurrentPositionStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['car:item:read']]),
        new GetCollection(
            normalizationContext: ['groups' => ['car:collection:read']]
        ),
        new Post(
            normalizationContext: ['groups' => ['car:create:read']],
            denormalizationContext: ['groups' => ['car:create:write']],
        ),
        new Post(
            uriTemplate: '/cars/{id}/positions',
            openapi: new Operation(
                summary: 'Creates CarRecordedGeolocation resource and updates Car\'s currentPosition property',
                description: 'Creates CarRecordedGeolocation resource and updates Car\'s currentPosition property'
            ),
            normalizationContext: ['groups' => ['car:update:current_position:read'], AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS => false],
            denormalizationContext: ['groups' => ['car:update:current_position:write']],
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
class Car
{
    public const REGISTRATION_REGEX = '@^[A-Z]{1}[A-Z\d]{2,11}$@';
    public const VIN_REGEX = '@^[A-Z]{1}[A-Z\d]{16}$@';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    #[Groups(['car:item:read', 'car:collection:read', 'car:create:read', 'car:update:read', 'rental:item:read', 'rental:collection:read', 'rental:update:read', 'rental:create:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: CarBrand::class)]
    #[Groups(['car:item:read', 'car:collection:read', 'car:create:read', 'car:create:write', 'car:update:read', 'car:update:write', 'rental:item:read', 'rental:collection:read', 'rental:update:read', 'rental:create:read'])]
    private ?CarBrand $brand;

    #[ORM\Column(length: 12, unique: true)]
    #[Assert\Regex(pattern: self::REGISTRATION_REGEX, message: 'Invalid registration')]
    #[Assert\Length(min: 3, max: 12)]
    #[ApiProperty(openapiContext: [
        'example' => 'ZK3666'
    ])]
    #[Groups(['car:item:read', 'car:collection:read', 'car:create:read', 'car:create:write', 'car:update:read', 'car:update:write', 'rental:item:read', 'rental:collection:read', 'rental:update:read', 'rental:create:read'])]
    private string $registration = '';

    #[ORM\Column(length: 17, unique: true)]
    #[Assert\Regex(pattern: self::VIN_REGEX, message: 'Invalid VIN')]
    #[Assert\Length(exactly: 17)]
    #[ApiProperty(
        openapiContext: [
            'example' => 'K9ITO0C2W2BR1N12M'
        ]
    )]
    #[Groups(['car:item:read', 'car:collection:read', 'car:create:read', 'car:create:write', 'car:update:read', 'rental:item:read', 'rental:collection:read', 'rental:update:read', 'rental:create:read'])]
    private string $vin = '';

    #[ORM\Embedded(class: RecordedGeolocation::class)]
    private ?RecordedGeolocation $currentPosition;

    #[Groups(['car:item:read', 'car:collection:read'])]
    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: 'car', cascade: ['persist'], orphanRemoval: true)]
    private Collection $rentals;

    public function __construct()
    {
        $this->currentPosition = new RecordedGeolocation;
        $this->rentals = new ArrayCollection();
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

    #[Groups(['car:item:read', 'car:collection:read', 'car:update:current_position:read', 'car:update:current_position:write', 'car:update:read'])]
//    #[ApiProperty]
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

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setCar($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getCar() === $this) {
                $rental->setCar(null);
            }
        }

        return $this;
    }

    #[Groups(['car:item:read', 'car:collection:read'])]
    public function getIsRented(): bool
    {
        return null !== $this->getLatestRental();
    }

    #[Groups(['car:item:read', 'car:collection:read'])]
    public function getLatestRental(): ?Rental
    {
        $latestRental = $this->rentals->last();

        if (false === $latestRental)
            return null;

        return $latestRental;
    }
}
