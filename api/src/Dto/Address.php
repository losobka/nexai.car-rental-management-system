<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

final readonly class Address
{
    public function __construct(
        #[Groups(['rental:item:write', 'rental:create:write'])]
        public string $street,
        #[Groups(['rental:item:write', 'rental:create:write'])]
        public string $postalCode,
        #[Groups(['rental:item:write', 'rental:create:write'])]
        public string $city
    ) {
    }
}