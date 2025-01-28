<?php

namespace App\Factory;

use App\Entity\Car;
use App\Enum\CarBrand;
use App\Service\RegistrationGenerator;
use App\Service\VinGenerator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Car>
 */
final class CarFactory extends PersistentProxyObjectFactory
{
    private static $vins = [];
    private static $registrations = [];

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(
        private readonly VinGenerator $vinGenerator,
        private readonly RegistrationGenerator $registrationGenerator
    ) {
    }

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
            'brand' => self::faker()->randomElement(CarBrand::cases()),
            'registration' => $this->uniqueRegistration(),
            'vin' => $this->uniqueVin(),
        ];

        if (self::faker()->randomElement([true, false])) {
            $defaults['currentPosition'] = RecordedGeolocationFactory::new([
                'latitude' => self::faker()->latitude(),
                'longitude' => self::faker()->longitude(),
                'recordedAt' => self::faker()->dateTime()
            ]);
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

    protected function uniqueVin(): string
    {
        do {
            $vin = $this->vinGenerator->generate();
        } while (in_array($vin, self::$vins));

        self::$vins[] = $vin;

        return $vin;
    }

    protected function uniqueRegistration(): string
    {
        do {
            $registration = $this->registrationGenerator->generate();
        } while (in_array($registration, self::$registrations));

        self::$registrations[] = $registration;

        return $registration;
    }
}
