<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Exception\OperationNotFoundException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Brand;
use App\Enum\CarBrand as CarBrandEnum;

final class CarBrandProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|null|array
    {
        if (false === $operation instanceof CollectionOperationInterface)
            throw new OperationNotFoundException;

        return array_map(fn (CarBrandEnum $carBrand) => new Brand($carBrand->value), CarBrandEnum::cases());
    }
}