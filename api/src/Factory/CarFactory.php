<?php

namespace App\Factory;

use App\Entity\Car;
use App\Enum\CarBrand;
use App\Service\RegistrationGenerator;
use App\Service\VinGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Car>
 */
class CarFactory extends PersistentProxyObjectFactory
{
    private static array $vins = [];
    private static array $registrations = [];

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(
        #[Autowire(service: 'app.service.vin_generator')]
        public  readonly VinGenerator $vinGenerator,
        #[Autowire(service: 'app.service.registration_generator')]
        public readonly RegistrationGenerator $registrationGenerator
    ) {
        parent::__construct();
    }

    public static function class(): string
    {
        return Car::class;
    }

    public function withPosition(): self
    {
        return $this->with([
            'currentPosition' => null
//            'currentPosition' => RecordedGeolocationFactory::new()->with([
//                'latitude' => self::faker()->latitude(),
//                'longitude' => self::faker()->longitude(),
//                'recordedAt' => self::faker()->dateTime()
//            ])]
        ]);
    }

    public function withoutPosition(): self
    {
        return $this->with([
                'currentPosition' => null
        ]);
    }

    public function withRentals(int $count = 10): self
    {
        return $this->with([
//            'rentals' => new ArrayCollection([])
            'rentals' => new ArrayCollection(RentalFactory::new()->createRange(1, $count, ['car' => CarFactory::new()->withoutPersisting()->create()]))
        ]);
    }

    public function withoutRentals(): self
    {
        return $this->with([
            'rentals' => new ArrayCollection([])
//            'rentals' => new ArrayCollection(RentalFactory::createRange(1, $count, ['car' => static::random()]))
        ]);
    }

    public function withTooLongVin(): self
    {
        return $this->with([
            'vin' => mb_substr(string: sha1(random_bytes(1024)), start: 0, length: 20)
        ]);
    }

    public function withTooShortVin(): self
    {
        return $this->with([
            'vin' => mb_substr(string: sha1(random_bytes(1024)), start: 0, length: 16)
        ]);
    }

    public function withVinThatStartsWithNumber(): self
    {
        return $this->with([
            'vin' => mb_substr(string: ((string) random_int(0, 9)) . sha1(random_bytes(1024)), start: 0, length: 16)
        ]);
    }

    public function withUniqueVin(): self
    {
        return $this->with([
            'vin' => $this->uniqueVin()
        ]);
    }

    public function withNonUniqueVin(): self
    {
        return $this->with([
            'vin' => self::$vins[random_int(0, count(self::$vins) - 1)]
        ]);
    }

    public function withValidBrand(): self
    {
        return $this->with([
            'brand' => CarBrand::values()[random_int(0, count(CarBrand::values()) - 1)]
        ]);
    }

    public function withInvalidBrand(): self
    {
        do {
            $brand = sha1(random_bytes(1024));
        } while(null !== CarBrand::tryFrom($brand));

        return $this->with([
//            'brand' => $brand,
            'brand' => 'invalid-brand'
        ]);
    }

    public function withValidVin(): self
    {
        return $this->with([
            'vin' => $this->uniqueVin()
        ]);
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

//        if (self::faker()->randomElement([true, false])) {
//            $defaults['currentPosition'] = RecordedGeolocationFactory::new();
//        }

        return $defaults;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
//            ->withoutPersisting();
            // ->afterInstantiate(function(Car $car): void {})
        ;
    }

    protected function uniqueVin(): string
    {
        do {
//            $vin = $this->vinGenerator->generate() ?? mb_substr(string: sha1(random_bytes(1024)), length:17, start: 0);
            $vin = $this->vinGenerator->generate();
        } while (in_array($vin, self::$vins));

        self::$vins[] = $vin;

        return mb_strtoupper($vin);
    }

    protected function uniqueRegistration(): string
    {
        do {
//            $registration = $this->registrationGenerator->generate() ?? mb_substr(string: sha1(random_bytes(1024)), length:random_int(3, 12), start: 0);
            $registration = $this->registrationGenerator->generate();
        } while (in_array($registration, self::$registrations));

        self::$registrations[] = $registration;

        return mb_strtoupper($registration);
    }
}
