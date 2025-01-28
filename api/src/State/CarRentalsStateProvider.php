<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Rental;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CarRentalsStateProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->entityManager->getRepository(Rental::class)->findBy(['car' => $uriVariables['carId']]);
    }
}