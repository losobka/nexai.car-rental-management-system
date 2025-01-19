<?php

namespace App\Service\Faker\Provider;

use App\Service\RegistrationNumberGenerator;
use App\Service\VinGenerator;
use Faker\Generator;
use Faker\Provider\Base;
use Random\RandomException;

class Car extends Base
{
    public static VinGenerator $vinGenerator;
    public static RegistrationNumberGenerator $registrationNumberGenerator;

    public function __construct(Generator $generator, VinGenerator $vinGenerator, RegistrationNumberGenerator $registrationNumberGenerator)
    {
        parent::__construct($generator);
        self::$vinGenerator = $vinGenerator;
        self::$registrationNumberGenerator = $registrationNumberGenerator;
    }

    public static function vin(): string
    {
        do {
            try {
                $vin = self::$vinGenerator->generate();
            } catch (RandomException) {
                unset($vin);
            }
        } while (isset($vin));

        return $vin;
    }

    public static function registrationNumber(): string
    {
        do {
            try {
                $registrationNumber = self::$registrationNumberGenerator->generate(
                    (bool) rand(0, 100)
                        ? null
                        : self::randomElement(RegistrationNumberGenerator::COMMON_CITY_PREFIXES)
                );
            } catch (RandomException) {
                unset($registrationNumber);
            }
        } while (isset($registrationNumber));

        return $registrationNumber;
    }
}