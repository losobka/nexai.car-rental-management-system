<?php

namespace App\Factory;

use App\Entity\Car;
use App\Service\VinGenerator;
use App\Service\RegistrationNumberGenerator;
use Random\RandomException;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Car>
 */
final class CarFactory extends PersistentProxyObjectFactory
{
    private RegistrationNumberGenerator $registrationNumberGenerator;
    private VinGenerator $vinGenerator;
    private array $generatedVins = [];
    private array $generatedRegistrationNumbers = [];


    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(RegistrationNumberGenerator $registrationNumberGenerator, VinGenerator $vinGenerator) {
        parent::__construct();
        $this->registrationNumberGenerator = $registrationNumberGenerator;
        $this->vinGenerator = $vinGenerator;
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
            'brand' => self::faker()->randomElement(Car::AVAILABLE_BRANDS),
            'currentLat' => $this->generateGeoCoordinate(),
            'currentLng' => $this->generateGeoCoordinate(),
            'registrationNumber' => $this->generateUniqueRegistrationNumber(),
            'rented' => self::faker()->boolean(),
            'vin' => $this->generateUniqueVin(),
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

    private function generateUniqueVin(): string
    {
        do {
            try {
                $vin = $this->vinGenerator->generate();
            } catch (RandomException) {
                unset($vin);
            }
        } while (isset($vin) && is_int(array_search($vin, $this->generatedVins, true)));

        return $vin;
    }

    private function generateUniqueRegistrationNumber(): string
    {
        do {
            try {
                $registrationNumber = $this->registrationNumberGenerator->generate(
                    (bool) rand(0, 100)
                        ? null
                        : self::faker()->randomElement(RegistrationNumberGenerator::COMMON_CITY_PREFIXES)
                );
            } catch (RandomException) {
                unset($registrationNumber);
            }
        } while (isset($registrationNumber) && is_int(array_search($registrationNumber, $this->generatedRegistrationNumbers, true)));

        return $registrationNumber;
    }

    private function generateGeoCoordinate(): float
    {
        return self::faker()->randomFloat(5, -90, 90);
    }
}
