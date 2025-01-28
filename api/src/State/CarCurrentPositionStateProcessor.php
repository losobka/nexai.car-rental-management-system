<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Embeddable\RecordedGeolocation;
use App\Entity\Car;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CarCurrentPositionStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation->getClass() !== Car::class || false === $operation instanceof Patch)
            return;

        $car = ($repository = $this->entityManager->getRepository(Car::class))->find($uriVariables['id']);

        if (null === $car)
            throw new NotFoundHttpException;

        $car->setCurrentPosition(new RecordedGeolocation($data->getLatitude(), $data->getLongitude()));

        $repository->getEntityManager()->flush();

    }
}
