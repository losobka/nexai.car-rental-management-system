<?php

namespace App\DataFixtures;

use App\Factory\CarFactory;
use App\Factory\RentalFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\faker;
final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CarFactory::createMany(10);

        foreach (CarFactory::repository()->findAll() as $car) {
            foreach (range(0, random_int(1, 10)) as $i) {
                $currentTime = faker()->dateTime();

                if (false === faker()->randomElement([true, false]))
                    continue;

                $currentTime = faker()->dateTimeBetween($currentTime, 'now');

                $rental = RentalFactory::new()->create([
                    'startDate' => $currentTime,
                    'endDate' => faker()->dateTimeBetween($currentTime, 'now'),
                    'car' => $car
                ]);
            }

            if (faker()->randomElement([true, false]))
                $car->getRentals()->last()->setEndDate(null);
        }
    }
}
