<?php

namespace App\Tests\Service;

use App\Service\VinGenerator;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class VinGeneratorTest extends TestCase
{
    private VinGenerator $vinGenerator;

    public function setUp(): void
    {
        parent::setUp();
        $this->vinGenerator = new VinGenerator();
    }

    public function testTheVinContainsOnlyUppercaseLettersAndNumbers():
    void
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
        $this->assertMatchesRegularExpression(sprintf('@^[A-Z]{1}[A-Z0-9]{%d}$@', (VinGenerator::LENGTH - 1))
        , $vin);
    }

    public function testTheFistCharacterOfVinIsALetter():
    void
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
        $this->assertLessThanOrEqual(VinGenerator::LENGTH, mb_strlen($vin));
        $this->assertLessThanOrEqual(VinGenerator::LENGTH, strlen($vin));
    }
}
