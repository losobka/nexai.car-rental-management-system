<?php

namespace App\Story;

use App\Factory\CarFactory;
use Zenstruck\Foundry\Story;

final class DefaultCarsStory extends Story
{
    public function build(): void
    {
        CarFactory::createMany(20);
    }
}
