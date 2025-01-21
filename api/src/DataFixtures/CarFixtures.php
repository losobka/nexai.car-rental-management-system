<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Service\LatitudeGenerator;
use App\Service\LongitudeGenerator;
use App\Service\RegistrationGenerator;
use App\Service\VinGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Random\RandomException;

final class CarFixtures extends Fixture
{   public function __construct(
        private readonly VinGenerator          $vinGenerator,
        private readonly RegistrationGenerator $registrationGenerator,
        private readonly LatitudeGenerator     $latitudeGenerator,
        private readonly LongitudeGenerator    $longitudeGenerator
    ) {
    }

    /**
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 100) as $step) {
            $car = $this->create();

            $manager->persist($car);
        }

        $manager->flush();
    }

    public function create(): Car
    {
        $car = new Car;
        $car->setBrand('Audi');
        $car->setRegistration($this->registrationGenerator->generate());
        $car->setVin($this->vinGenerator->generate());
        $car->setRented((bool) random_int(0, 1));
        $car->setLatitude($this->latitudeGenerator->generate());
        $car->setLongitude($this->longitudeGenerator->generate());

        if ($car->isRented()) {
//                $car->setCustomerEmail($this->faker->email());
//                $car->setCustomerAddress(mb_substr('ul. ' . $this->faker->address(), 0, 64));
        }

        return $car;
    }
}
