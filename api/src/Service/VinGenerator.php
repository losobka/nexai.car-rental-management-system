<?php

declare(strict_types=1);

namespace App\Service;

use Random\RandomException;

class VinGenerator
{
    /**
     * @throws RandomException
     */
    public function generate(): string
    {
        return mb_strtoupper(chr(random_int(65, 90)) . mb_substr(md5(random_bytes(1024)), 0, 16));
    }
}