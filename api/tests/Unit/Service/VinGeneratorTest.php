<?php

namespace App\Tests\Unit\Service;

use App\Entity\Car;
use App\Service\VinGenerator;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VinGeneratorTest extends KernelTestCase
{
    private VinGenerator $vinGenerator;
    public function __construct(string $name) {
        parent::__construct($name);
        $this->vinGenerator = self::getContainer()->get(VinGenerator::class);
    }

    public function testTheVinContainsOnlyUppercaseLettersAndNumbers(): void
    {
        // given
        $vinGenerator = $this->vinGenerator;

        // when
        try {
            $vin = $vinGenerator->generate();
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }

        // then
        $this->assertMatchesRegularExpression(Car::VIN_REGEX, $vin);
    }

    public function testTheFistCharacterOfVinIsALetter(): void
    {
        // given
        $vinGenerator = $this->vinGenerator;
        $allowedPrefixLetters = range('A', 'Z');

        // when
        try {
            $vin = $vinGenerator->generate();
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }

        // then
        $this->assertContains(mb_substr($vin, 0, 1), $allowedPrefixLetters);
    }

    public function testGeneratedVinsLengthIsCorrect(): void
    {
        // given
        $vinGenerator = $this->vinGenerator;

        // when
        try {
            $vin  = $vinGenerator->generate();
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }

        // then
        $this->assertLessThanOrEqual(17, mb_strlen($vin));
        $this->assertLessThanOrEqual(17, strlen($vin));
    }
}
