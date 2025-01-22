<?php

namespace App\Tests\Unit\Service\Generator;

use App\Service\RegistrationGenerator;
use OutOfBoundsException;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RegistrationGeneratorTest extends KernelTestCase
{
    private RegistrationGenerator $registrationGenerator;
    private int $registrationMaxLength;

    public function setUp(): void
    {
        parent::setUp();
        $this->registrationGenerator = self::getContainer()->get(RegistrationGenerator::class);
        $this->registrationMaxLength = self::getContainer()->getParameter('app.registration.max_length');
    }

    public function testTheFistCharacterOfGeneratedVinIsAUppercaseLetter(): void
    {
        // given
        $registrationGenerator = $this->registrationGenerator;
        $allowedPrefixLetters = range('A', 'Z');

        // when
        try {
            $registration = $registrationGenerator->generate();
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }

        // then
        $this->assertContains(mb_substr($registration, 0, 1), $allowedPrefixLetters);
    }

    public function testGeneratorThrowsOutOfBoundExceptionWhenProvidedPrefixIsLongerThanAllowedRegistrationNumberLength
    (): void
    {
        // given
        $registrationGenerator = $this->registrationGenerator;

        // then
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(sprintf(
            'Registration number cannot be longer than %d characters',
            $this->registrationMaxLength
        ));

        // when
        try {
            $registrationGenerator->generate(mb_str_pad('T', $this->registrationMaxLength + 1));
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }
    }

    public function testGeneratedRegistrationNumberLengthIsCorrect(): void
    {
        // given
        $registrationGenerator = $this->registrationGenerator;

        // when
        try {
            $registration = $registrationGenerator->generate();
        } catch (RandomException $e) {
            $this->markTestIncomplete($e->getMessage());
        }

        // then
        $this->assertLessThanOrEqual($this->registrationMaxLength, mb_strlen($registration));
        $this->assertLessThanOrEqual($this->registrationMaxLength, strlen($registration));
    }
}
