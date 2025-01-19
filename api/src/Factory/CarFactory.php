<?php

namespace App\Factory;

use App\Entity\Car;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Car>
 */
final class CarFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Car::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $defaults = [
            'brand' => self::faker()->randomElement(Car::AVAILABLE_BRANDS),
            'currentLat' => self::faker()->latitude(),
            'currentLng' => self::faker()->longitude(),
            'registrationNumber' => self::faker()->carRegistrationNumber(),
            'rented' => self::faker()->randomElement([true, false]),
            'vin' => self::faker()->vin(),
        ];

        if ($defaults['rented']) {
            $defaults['customerEmail'] = mb_strtolower(self::faker()->email());
            $defaults['customerAddress'] = 'ul. ' . self::faker()->address();
        }

        return $defaults;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Car $car): void {})
        ;
    }
}
