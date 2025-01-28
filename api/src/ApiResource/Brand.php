<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\CarBrandProvider;
use App\Dto\CarBrand as CarBrandDto;

#[ApiResource(uriTemplate: '/brands', operations: [new GetCollection], provider: CarBrandProvider::class)]
final class Brand extends CarBrandDto
{
}