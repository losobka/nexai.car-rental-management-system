<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response;
use App\Dto\CarRental;
use App\Embeddable\Address;
use App\State\CarRentalsStateProvider;
use App\State\RentalFinishStateProcessor;
use ArrayObject;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/cars/{carId}/rentals/{id}',
            uriVariables: [
                'carId' => new Link(toProperty: 'car', fromClass: Car::class),
                'id' => new Link(toClass: Rental::class),
            ],
            normalizationContext: ['groups' => ['rental:item:read']]
        ),
        new Patch(
            uriTemplate: '/cars/{carId}/rentals/{id}',
            uriVariables: [
                'carId' => new Link(toProperty: 'car', fromClass: Car::class),
                'id' => new Link(toClass: Rental::class),
            ],
            normalizationContext: ['groups' => ['rental:item:read']],
            denormalizationContext: ['groups' => ['rental:item:write']],
            input: CarRental::class,
//            processor: new RentalUpdateNotFinishedStateProcessor()
        ),
        new GetCollection(
            uriTemplate: '/cars/{carId}/rentals',
            uriVariables: [
                'carId' => new Link(toProperty: 'car', fromClass: Car::class),
            ],
            normalizationContext: ['groups' => ['rental:collection:read']]
        ),
        new Post(
            uriTemplate: '/cars/{carId}/rentals',
            uriVariables: [
//                'carId' => new Link(toProperty: 'car', fromClass: Car::class),
                'carId' => new Link(toProperty: 'car', fromClass: Car::class, toClass: Rental::class),
            ],
            normalizationContext: ['groups' => ['rental:create:read']],
            denormalizationContext: ['groups' => ['rental:create:write']],
            provider: CarRentalsStateProvider::class
        ),
        new Patch(
            uriTemplate: '/cars/{carId}/rentals/current/perform-return',
            uriVariables: [
                'carId' => new Link(toProperty: 'car', fromClass: Rental::class, toClass: Car::class, description: 'Car identifier'),
            ],
            status: 204,
            openapi: new Operation(
                responses: [
                    204 => new Response(description: 'Rental resource endDate updated'),
                ],
                requestBody: new RequestBody(
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => []
                            ]
                        ]
                    ]),
                    required: false
                )
            ),
            normalizationContext: ['groups' => ['rental:perform_return:read']],
            denormalizationContext: ['groups' => ['rental:perform_return:write']],
            provider: CarRentalsStateProvider::class,
            processor: RentalFinishStateProcessor::class
        )
    ],
)]

class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    #[Groups(['rental:item:read', 'rental:collection:read', 'rental:create:read', 'rental:update:read', 'car:item:read', 'car:collection:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\Email]
    #[ApiProperty(openapiContext: [
        'description' => 'Please type only when the car is rented'
    ])]
    #[Groups(['rental:item:read', 'rental:collection:read', 'rental:create:read', 'rental:create:write', 'rental:update:read', 'car:item:read', 'car:collection:read'])]
    private ?string $customerEmail = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['rental:item:read', 'rental:collection:read', 'rental:create:read', 'rental:update:read', 'car:item:read', 'car:collection:read'])]
    private DateTimeInterface $startDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['rental:item:read', 'rental:collection:read', 'rental:create:read', 'rental:update:read', 'car:item:read', 'car:collection:read'])]
    private ?DateTimeInterface $endDate = null;

    #[ORM\Embedded(class: Address::class, columnPrefix: 'billing_')]
    #[Groups(['rental:item:read', 'rental:item:write', 'rental:collection:read', 'rental:create:read', 'rental:create:write', 'rental:update:read', 'car:item:read', 'car:collection:read'])]
    private Address $billingAddress;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rental:item:read', 'rental:create:write', 'rental:collection:read', 'rental:create:read', 'rental:create:write', 'rental:update:read'])]
    private ?Car $car = null;

    public function __construct()
    {
        $this->billingAddress = new Address;
        $this->startDate = new DateTime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['car:item:read', 'car:collection:read', 'rental:create:read', 'rental:create:write', 'rental:update:read', 'rental:update:write'])]
    public function getBillingAddress(): ?Address
    {
        if (
            empty($this->billingAddress?->getStreet())
                || empty($this->billingAddress?->getCity())
                || empty($this->billingAddress?->getPostalCode())
        )
                return null;

        return $this->billingAddress;
    }
    public function setBillingAddress(Address $billingAddress): void
    {
        if (
            empty($billingAddress?->getStreet())
            || empty($billingAddress?->getCity())
            || empty($billingAddress?->getPostalCode())
        )
            $billingAddress = new Address;

        $this->billingAddress = $billingAddress;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(?string $customerEmail): void
    {
        $this->customerEmail = $customerEmail;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }
}
