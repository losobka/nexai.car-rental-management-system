<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Car;
use App\Entity\Rental;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final readonly class RentalFinishStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation->getClass() !== Rental::class || false === $operation instanceof Patch)
            return;

        $car = ($repository = $this->entityManager->getRepository(Car::class))->find($uriVariables['carId']);
        if (null === $car || false === ($rental = $car->getLatestRental()))
            throw new NotFoundHttpException;

        if (null !== $rental->getEndDate())
            throw new UnprocessableEntityHttpException('The Rental is already finished');

        $rental->setEndDate(new DateTime);

        $repository->getEntityManager()->flush();

    }
}
