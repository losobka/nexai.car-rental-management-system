<?php

namespace App\Story;

use App\Entity\Car;
use App\Service\RegistrationNumberGenerator;
use App\Service\VinGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Zenstruck\Foundry\Story;

final class DefaultCarsStory extends Story
{
    private Generator $faker;
    private EntityManagerInterface $entityManager;
    private VinGenerator $vinGenerator;
    private RegistrationNumberGenerator $registrationNumberGenerator;

    public function __construct(Generator $faker, EntityManagerInterface $entityManager, VinGenerator $vinGenerator, RegistrationNumberGenerator $registrationNumberGenerator)
    {
        $this->faker = $faker;
        $this->entityManager = $entityManager;
        $this->vinGenerator = $vinGenerator;
        $this->registrationNumberGenerator = $registrationNumberGenerator;
    }
    public function build(): void
    {
        $this->loadMany(20);
    }

    public function loadMany(int $count): void
    {
        if ($count < 1)
            return;

        foreach (range(1, $count) as $step) {
            $car = new Car;
            $car->setBrand($this->faker->randomElement(Car::AVAILABLE_BRANDS));
            $car->setRegistrationNumber($this->registrationNumberGenerator->generate());
            $car->setVin($this->vinGenerator->generate());
            $car->setRented($this->faker->randomElement([true, false]));
            $car->setLatitude($this->faker->latitude());
            $car->setLongitude($this->faker->longitude());

            if ($car->isRented()) {
                $car->setCustomerEmail($this->faker->email());
                $car->setCustomerAddress(mb_substr('ul. ' . $this->faker->address(), 0, 64));
            }

            $this->entityManager->persist($car);
        }

        $this->entityManager->flush();
    }
}