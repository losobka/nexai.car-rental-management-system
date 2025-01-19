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
            'registrationNumber' => self::faker()->unique()->registrationNumber(),
            'vin' => self::faker()->unique()->vin(),
            'rented' => self::faker()->randomElement([true, false]),
            'latitude' => self::faker()->latitude(),
            'longitude' => self::faker()->longitude(),
        ];

        if ($defaults['rented']) {
            $defaults['customerEmail'] = mb_strtolower(self::faker()->email());
            $defaults['customerAddress'] = mb_substr('ul. ' . self::faker()->address(), 0, 64);
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
