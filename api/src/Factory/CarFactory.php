<?php

namespace App\Factory;

use App\Entity\Car;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Car>
 */
final class CarFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Car::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $defaults = [
            'brand' => Car::AVAILABLE_BRANDS[array_rand(Car::AVAILABLE_BRANDS)],
            'currentLat' => self::faker()->randomFloat(5, -90, 90),
            'currentLng' => self::faker()->randomFloat(5, -90, 90),
            'registrationNumber' => mb_strtoupper(self::faker()->text(12)),
            'rented' => self::faker()->boolean(),
            'vin' => mb_strtoupper(self::faker()->text(17)),
        ];

        if ($defaults['rented']) {
            $defaults['customerEmail'] = mb_strtolower(self::faker()->email());
            $defaults['customerAddress'] = mb_strtoupper(self::faker()->address());
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
