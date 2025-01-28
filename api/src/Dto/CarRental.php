<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

final readonly class CarRental
{
    public function __construct(
        #[Groups(['rental:item:write'])]
        public string  $email,
        #[Groups(['rental:item:write'])]
        public Address $billingAddress
    ) {
    }
}