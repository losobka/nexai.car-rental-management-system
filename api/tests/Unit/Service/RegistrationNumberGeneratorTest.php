<?php

namespace App\Tests\Unit\Service;

use App\Service\RegistrationNumberGenerator;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class RegistrationNumberGeneratorTest extends TestCase
{
    private RegistrationNumberGenerator $registrationNumberGenerator;

    public function setUp(): void
    {
        parent::setUp();
        $this->registrationNumberGenerator = new RegistrationNumberGenerator();
    }

    public function testTheFistCharacterOfGeneratedVinIsAUppercaseLetter(): void
    {
        // given
        $registrationNumberGenerator = $this->registrationNumberGenerator;
        $allowedPrefixLetters = range('A', 'Z');

        // when
        try {
            $registrationNumber = $registrationNumberGenerator->generate();
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }

        // then
        $this->assertContains(mb_substr($registrationNumber, 0, 1), $allowedPrefixLetters);
    }

    public function testGeneratorThrowsOutOfBoundExceptionWhenProvidedPrefixIsLongerThanAllowedRegistrationNumberLength
    (): void
    {
        // given
        $registrationNumberGenerator = $this->registrationNumberGenerator;

        // then
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(sprintf(
            'Registration number cannot be longer than %d characters',
            RegistrationNumberGenerator::MAX_LENGTH
        ));

        // when
        try {
            $registrationNumberGenerator->generate(mb_str_pad('T', RegistrationNumberGenerator::MAX_LENGTH + 1));
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }
    }

    public function testGeneratedRegistrationNumberLengthIsCorrect(): void
    {
        // given
        $registrationNumberGenerator = $this->registrationNumberGenerator;

        // when
        try {
            $registrationNumber = $registrationNumberGenerator->generate();
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }

        // then
        $this->assertLessThanOrEqual(RegistrationNumberGenerator::MAX_LENGTH, mb_strlen($registrationNumber));
        $this->assertLessThanOrEqual(RegistrationNumberGenerator::MAX_LENGTH, strlen($registrationNumber));
    }
}
