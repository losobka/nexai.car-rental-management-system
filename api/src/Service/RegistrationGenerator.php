<?php

declare(strict_types=1);

namespace App\Service;

use OutOfBoundsException;
use Random\RandomException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RegistrationGenerator
{   public function __construct(
        #[Autowire('%app.registration.max_length%')]
        private readonly int $maxLength
    ) {
    }

    /**
     * @throws RandomException
     * @throws OutOfBoundsException
     */
    public function generate(string | null $prefix = null): string
    {
        if (null === $prefix)
            $prefix = chr(rand(65, 90));

        if (($prefixLength = (int) mb_strlen($prefix)) > $this->maxLength)
            throw new OutOfBoundsException(sprintf('Registration number cannot be longer than %d characters',
                $this->maxLength));

        return mb_strtoupper(
            $prefix
                . mb_substr(
                    md5(random_bytes(1024)),
                    0,
                    random_int($prefixLength + 1, $this->maxLength - $prefixLength)
                )
        );
    }
}