<?php

namespace App\Factory;

use App\Embeddable\RecordedGeolocation;
use Zenstruck\Foundry\ObjectFactory;

/**
 * @extends ObjectFactory<RecordedGeolocation>
 */
final class RecordedGeolocationFactory extends ObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return RecordedGeolocation::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'recordedAt' => self::faker()->dateTime(),
            'latitude' => self::faker()->latitude(),
            'longitude' => self::faker()->longitude()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(RecordedGeolocation $recordedGeolocation): void {})
        ;
    }
}
