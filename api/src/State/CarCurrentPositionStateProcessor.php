<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Embeddable\RecordedGeolocation;
use App\Entity\Car;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarCurrentPositionStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation::class !== Post::class)
            return;

        $car = ($repository = $this->entityManager->getRepository(Car::class))->find($uriVariables['id']);

        if (null === $car)
            throw new NotFoundHttpException;

        $car->setCurrentPosition(new RecordedGeolocation($data->getLatitude(), $data->getLongitude()));

        $repository->getEntityManager()->flush();

    }
}
