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
    private function countOfCarsInCollectionProvider(): iterable
    {
        yield 'empty' => [0];
        yield 'with 20' => [20];
        yield 'with 1000' => [1000];
    }
}
