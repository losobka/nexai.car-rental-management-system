<?php

namespace App\Tests\Unit\Service\Generator;

use App\Entity\Car;
use App\Service\VinGenerator;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\BeforeClass;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Service\Attribute\Required;

class VinGeneratorTest extends KernelTestCase
{
//    #[Autowire(expression: 'service("@app.service.vin_generator"(')]
//    #[Required]
//    #[Autowire(VinGenerator::class)]
    private VinGenerator $vinGenerator;
    public function __construct($name) {
        parent::__construct($name);
        $this->vinGenerator = self::getContainer()->get('app.service.vin_generator');
//        $this->vinGenerator = $vinGenerator;
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
