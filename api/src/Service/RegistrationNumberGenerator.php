<?php

declare(strict_types=1);

namespace App\Service;

use OutOfBoundsException;
use Random\RandomException;

class RegistrationNumberGenerator
{
    public const MAX_LENGTH = 12;
    public const COMMON_CITY_PREFIXES = [
        'WA',
        'KR',
        'PO',
        'GD',
        'WR',
        'LU',
        'BI',
        'SZ',
        'KI',
        'OL',
        'KA',
        'RZ',
        'ZK',
        'OP',
        'EL',
        'PL',
        'RA',
        'TB',
        'OS',
        'WN',
    ];

    /**
     * @throws RandomException
     * @throws OutOfBoundsException
     */
    public function generate(string | null $prefix = null): string
    {
        if (null === $prefix)
            $prefix = chr(rand(65, 90));

        if (($prefixLength = (int) mb_strlen($prefix)) > self::MAX_LENGTH)
            throw new OutOfBoundsException(sprintf('Registration number cannot be longer than %d characters',
                self::MAX_LENGTH));

        return mb_strtoupper(
            $prefix
                . mb_substr(
                    md5(random_bytes(1024)),
                    0,
                    random_int($prefixLength + 1, self::MAX_LENGTH - $prefixLength)
                )
        );
    }
}