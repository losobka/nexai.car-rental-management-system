<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Car;
use App\Factory\CarFactory;
use App\Repository\CarRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Zenstruck\Foundry\Persistence\Proxy;

class CarTest extends ApiTestCase
{
    /**
     * @dataProvider countOfCarsInCollectionProvider
     */
    public function testShouldReturnCollectionOfCarsWithNElements(int $expectedCountOfCars): void
    {
        // given
        CarFactory::createMany($expectedCountOfCars);

        $countOfCarsByCarFactory = self::getContainer()->get(CarFactory::class)::count();
        $countOfCarsByCarRepository = self::getContainer()->get(CarRepository::class)->count();
        $httpClient = static::createClient(defaultOptions: ['headers'=> ['Accept' => 'application/json']]);
        $serializer = self::getContainer()->get(SerializerInterface::class);

        // when
        $response = $httpClient->request('GET', '/cars');

        // then
        $deserializedResponse = $response->toArray(false);
        $cars = array_map(fn (Proxy $proxy) => $proxy->_real(),CarFactory::all());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('Content-Type','application/json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Car::class);
        $this->assertSame(
            $serializer->normalize($cars, 'json', [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]),
            $serializer->normalize($response->toArray(false), 'json', [AbstractObjectNormalizer::SKIP_NULL_VALUES => true])
        );
        $this->assertCount($expectedCountOfCars,  $deserializedResponse);
        $this->assertSame($expectedCountOfCars, $countOfCarsByCarFactory);
        $this->assertSame($expectedCountOfCars, $countOfCarsByCarRepository);

        CarFactory::truncate();
    }

    /**
     * @dataProvider validCarProvider
     */
    public function testShouldCreateACar(int $expectedId, string $brand, string $registrationNumber, string $vin, bool $rented, string | null $customerEmail, string | null $customerAddress): void
    {
        // given
        $car = (object) [
            'brand' => 'BMW',
            'registrationNumber' => 'TEST123',
            'vin' => 'DFMDU34X8MUD84229',
            'rented' => $rented,
            'customerEmail' => $customerEmail,
            'customerAddress' => $customerAddress
        ];
        $httpClient = static::createClient(defaultOptions: ['headers'=> ['Accept' => 'application/json']]);
        $serializer = self::getContainer()->get(SerializerInterface::class);

        // when
        $response = $httpClient->request('POST', '/cars', ['json' => $car]);

        // then
        $deserializedResponse = $response->toArray(false);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type','application/json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Car::class);
        $this->assertSame(
            $serializer->normalize(
                array_merge(
                    ['id' => $expectedId],
                    array_filter((array) $car, fn ($value) => null !== $value),
                    ['latitude' => 0.0, 'longitude' => 0.0]
                ),
                'json',
                [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]
            ),
            $serializer->normalize($deserializedResponse, 'json', [AbstractObjectNormalizer::SKIP_NULL_VALUES => true])
        );

        CarFactory::truncate();
    }

    /**
     * @dataProvider invalidCarProvider
     */
    public function testShouldNotCreateACar(int $expectedId, string $brand, string $registrationNumber, string $vin, bool $rented, string | null $customerEmail, string | null $customerAddress, string $expectedMessage): void
    {
        // given
        $car = (object) [
            'brand' => $brand,
            'registrationNumber' => $registrationNumber,
            'vin' => $vin,
            'rented' => $rented,
            'customerEmail' => $customerEmail,
            'customerAddress' => $customerAddress
        ];
        $httpClient = static::createClient(defaultOptions: ['headers'=> ['Accept' => 'application/json']]);

        // when
        $response = $httpClient->request('POST', '/cars', ['json' => $car]);

        // then
        $deserializedResponse = $response->toArray(false);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseHeaderSame('Content-Type','application/problem+json; charset=utf-8');
        $this->assertCount(1, $deserializedResponse['violations']);
        $this->assertSame($expectedMessage, $deserializedResponse['violations'][0]['message']);

        CarFactory::truncate();
    }
    private function countOfCarsInCollectionProvider(): iterable
    {
        yield 'empty' => [0];
        yield '20' => [20];
    }

    private function validCarProvider(): iterable
    {
        yield 'not rented' => [1, 'BMW', 'TEST123', 'DFMDU34X8MUD84229', false, null, null];
        yield 'rented' => [2, 'Audi', 'WA4567', 'YV1RS61T532259048', true, 'imie.nazwisko@example.nextai', 'ul. Sezamkowa, 11-222 Warszawa'];
    }

    private function invalidCarProvider(): iterable
    {
        yield 'with incorrect brand' => [1, 'incorrect-brand', 'TEST123', 'DFMDU34X8MUABC229', false, null, null, 'The value you selected is not a valid choice.'];
        yield 'with too short registration number' => [2, 'Audi', 'T', 'KMHCM36C05U123456', false, null, null, 'Invalid registration number'];
        yield 'with too long registration number' => [3, 'BMW', 'T123456789ABC', 'DFMDU34X8MUD84229', false, null, null, 'Invalid registration number'];
        yield 'with invalid registration number (number as first character)' => [4, 'Toyota', '1INVALID', 'DFMDU34X8MUD84229', false, null, null, 'Invalid registration number'];
        yield 'with invalid vin (number as first character)' => [5, 'Honda', '1INVALID', '9FMDU34X8MUD8422D', false, null, null, 'Invalid registration number'];
        yield 'rented with invalid customer email' => [6, 'Tesla', 'KR1234AB', 'HGCM82633A1234567', true, 'invalid_email', 'ul. Sezamkowa 5, 11-222 WarszAwa', 'This value is not a valid email address.'];
        yield 'not rented with customer email' => [7, 'Ferrari', 'WA5678CD', 'WDBUF56J76A123456', false, 'imie.nazwisko@example.nextai', null, 'Customer email address should be empty'];
        yield 'not rented with customer address' => [8, 'Volvo', 'PO4321EF', 'WBA3A5C59FF123456', false, null, 'ul. Sezamkowa, 11-222 Warszawa', 'Customer address should be empty'];
    }
}
