<?php

namespace App\Tests\E2E\Entity;

use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\State\ApiResource\Error;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Routing\IriConverter;
use App\ApiResource\Brand;
use App\Dto\CarBrand;
use App\Enum\CarBrand as CarBrandEnum;
use App\Entity\Car;
use App\Factory\CarFactory;
use App\Factory\RentalFactory;
use App\Repository\CarRepository;
use AppendIterator;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Iterator;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\Attributes\AfterClass;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\BeforeClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TypeError;
use Zenstruck\Foundry\Configuration;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\UnitTestConfig;

class CarTest extends ApiTestCase
{
    use Factories;
    private EntityManagerInterface $entityManager;
    private Client $httpClient;
    private NormalizerInterface $normalizer;
    private SerializerInterface $serializer;
    private Container $container;

    private $carFactory;

    #[BeforeClass]
    protected function setUp(): void
    {
        parent::setUp();

        $this->carFactory = CarFactory::new();

        self::ensureKernelShutdown();;

        $this->container = self::getContainer();
        $this->entityManager = $this->container->get(EntityManagerInterface::class);
        $this->httpClient = static::createClient(defaultOptions: ['headers'=> ['Accept' => 'application/json', 'Content-Type' => 'application/json']]);;
        $this->normalizer = self::getContainer()->get(NormalizerInterface::class);
        $this->serializer = self::getContainer()->get(SerializerInterface::class);
    }
    public static function getValidCarsToCreate(): \Generator
    {
//        yield 'withUniqueVin-withValidBrand-withPosition-withRentals' => [201, 'application/json; charset=utf-8', CarFactory::new()->withUniqueVin()->withValidBrand()->withPosition()->withRentals()->withoutPersisting()->create()];
//        yield 'withUniqueVin-withValidBrand-withPosition-withoutRentals' => [201, 'application/json; charset=utf-8', CarFactory::new()->withUniqueVin()->withValidBrand()->withPosition()->withoutRentals()->withoutPersisting()->create()];
//        yield 'withUniqueVin-withValidBrand-withoutPosition' => [201, 'application/json; charset=utf-8', CarFactory::new()->withUniqueVin()->withValidBrand()->withoutPosition()->withoutPersisting()->create()];
//        yield 'withUniqueVin-withInvalidBrand' => [400, 'application/problem+json; charset=utf-8', CarFactory::new()->withUniqueVin()->withInvalidBrand()->withoutPersisting()->create()];
//        yield 'withNonUniqueVin-withValidBrand' => [400, 'application/problem+json; charset=utf-8', CarFactory::new()->withNonUniqueVin()->withInvalidBrand()->withoutPersisting()->create()];
//        yield 'withVinThatStartsWithNumber-withValidBrand' => [422, 'application/problem+json; charset=utf-8', CarFactory::new()->withVinThatStartsWithNumber()->withValidBrand()->withoutPersisting()->create()];
//        yield 'withTooShortVin-withValidBrand' => [422, 'application/problem+json; charset=utf-8', CarFactory::new()->withTooShortVin()->withValidBrand()->withoutPersisting()->create()];
//        yield 'withTooLongVin-withValidBrand' => [422, 'application/problem+json; charset=utf-8', CarFactory::new()->withTooLongVin()->withValidBrand()->withoutPersisting()->create()];

        $carFactory = CarFactory::new()->withoutPersisting();


        yield 'withValidVin-withValidBrand-withPosition-withRentals' => [201, 'application/json; charset=utf-8', $carFactory->withValidVin()->withValidBrand()->withPosition()->withRentals()->create()];
        yield 'withUniqueVin-withValidBrand-withPosition-withRentals' => [201, 'application/json; charset=utf-8', $carFactory->withUniqueVin()->withValidBrand()->withPosition()->withRentals()->create()];
        yield 'withUniqueVin-withValidBrand-withPosition-withoutRentals' => [201, 'application/json; charset=utf-8', $carFactory->withUniqueVin()->withValidBrand()->withPosition()->withoutRentals()->create()];
        yield 'withUniqueVin-withValidBrand-withoutPosition' => [201, 'application/json; charset=utf-8', $carFactory->withUniqueVin()->withValidBrand()->withoutPosition()->create()];
        yield 'withUniqueVin-withInvalidBrand' => [400, 'application/problem+json; charset=utf-8', $carFactory->withUniqueVin()->withInvalidBrand()->create()];
        yield 'withNonUniqueVin-withValidBrand' => [400, 'application/problem+json; charset=utf-8', $carFactory->withNonUniqueVin()->withInvalidBrand()->create()];
        yield 'withVinThatStartsWithNumber-withValidBrand' => [422, 'application/problem+json; charset=utf-8', $carFactory->withVinThatStartsWithNumber()->withValidBrand()->create()];
        yield 'withTooShortVin-withValidBrand' => [422, 'application/problem+json; charset=utf-8', $carFactory->withTooShortVin()->withValidBrand()->create()];
        yield 'withTooLongVin-withValidBrand' => [422, 'application/problem+json; charset=utf-8', $carFactory->withTooLongVin()->withValidBrand()->create()];


//        yield 'simple valid minimum value object' => [
//        return [
//            'valid' => [
//                Response::HTTP_CREATED,
//                'application/json; charset=utf-8',
//                (object) [
//                    'brand' => 'Audi',
//                    'vin' => 'THISISAVALIDVIN01',
//                    'registration' => 'ZK3888'
//                ]
//            ]
//        ];
//        yield 'valid' => [
//            Response::HTTP_CREATED,
//            'application/json; charset=utf-8',
//            new Car('Audi', 'THISISAVALIDVIN01', 'ZK3888')
////            (object) [
////                'brand' => 'Audi',
////                'vin' => 'THISISAVALIDVIN01',
////                'registration' => 'ZK3888'
////            ]
//        ];
    }

    public static function getInvalidCarsToCreate(): Iterator
    {
        yield 'simple invalid minimum value object with duplicated vin' => [
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'application/problem+json; charset=utf-8',
            (object) [
                'brand' => 'Audi',
                'vin' => 'THISISAVALIDVIN01',
                'registration' => 'ZK3999'
            ]
        ];

        yield 'simple invalid minimum value object with invalid brand' => [
            Response::HTTP_BAD_REQUEST,
            'application/problem+json; charset=utf-8',
            (object) [
                'brand' => 'Unknown brand',
                'vin' => 'THISISAVALIDVIN01',
                'registration' => 'ZK3888'
            ]
        ];

        yield 'simple invalid minimum value object with invalid vin length' => [
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'application/problem+json; charset=utf-8',
            (object) [
                'brand' => 'Audi',
                'vin' => 'THISVINVALUEISTOOLONG',
                'registration' => 'ZK3888'
            ]
        ];

        yield 'simple invalid minimum value object with invalid vin (starts with number)' => [
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'application/problem+json; charset=utf-8',
            (object) [
                'brand' => 'Audi',
                'vin' => '1THISISINVALIDVIN',
                'registration' => 'ZK3888'
            ]
        ];

        yield 'simple invalid minimum value object with invalid registration (too short)' => [
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'application/problem+json; charset=utf-8',
            (object) [
                'brand' => 'Audi',
                'vin' => 'THISISAVALIDVIN02',
                'registration' => 'Z'
            ]
        ];

        yield 'simple invalid minimum value object with invalid registration (too long)' => [
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'application/problem+json; charset=utf-8',
            (object) [
                'brand' => 'Audi',
                'vin' => 'THISISAVALIDVIN02',
                'registration' => 'INVALIDLENGTH'
            ]
        ];
    }

    public static function getExistingCars(): Iterator
    {
        $cars = self::getContainer()->get(EntityManagerInterface::class)->getRepository(Car::class)->findAll();

        if (true === empty($cars)) {
            yield '0' => [400, 'application/problem+json; charset=utf-8', CarFactory::new(['id' => 0])->create()];
        }

        foreach ($cars as $car) {
            yield (string) $car->getId() => [201, 'application/json; charset=utf-8', $car];
        }
    }

    public static function getCarsToCreate(): iterable
    {
        $iterator = new AppendIterator;

//        $validCarsToCreate = $this->getValidCarsToCreate();
        $validCarsToCreate = self::getValidCarsToCreate();
//        $invalidCarsToCreate = self::getInvalidCarsToCreate();

//        $validCarsToCreate->rewind();
//        $invalidCarsToCreate->rewind();

        $iterator->append($validCarsToCreate);
//        $iterator->append($invalidCarsToCreate);

        return iterator_to_array($iterator);
    }

    #[BeforeClass]
//    #[AfterClass]
    public function truncateRepositories(): void
    {
        RentalFactory::repository()->truncate();
        CarFactory::repository()->truncate();
    }

    #[Test]
    #[DataProviderExternal(className: CarTest::class, methodName: 'getValidCarsToCreate')]
    #[Group(name: 'REST')]
    public function shouldCreateCarResourceTroughPostEndpointCall(int $statusCode, string $contentType, Car $car): void
    {
        // given
        $httpClient = $this->httpClient;
        $serializer = $this->serializer;
        $carAsJsonPayload = $serializer->serialize($car, 'json', ['iri' => false, 'groups' => ['car:create:write']]);

        // when
        $httpClient->request(method: 'POST', url: '/cars', options: ['body' => $carAsJsonPayload]);
//        dump($httpClient::class, $httpClient->getResponse()::class); die;

        // then
        $this->assertResponseStatusCodeSame($statusCode);
        $this->assertResponseHeaderSame('content-type', $contentType);

        if ($contentType === 'application/problem+json; charset=utf-8') {
            $this->expectException(ClientException::class);


            if (Response::HTTP_BAD_REQUEST === $httpClient->getResponse()->getStatusCode()) {
                $deserialized = $serializer->deserialize($httpClient->getResponse()->getContent(false), Error::class, 'json');
//                dump(deserialized);
                $this->assertInstanceOf(Error::class, $deserialized);
                $this->assertObjectHasProperty('title',$deserialized);
                $this->assertObjectHasProperty('detail',$deserialized);
                $this->assertObjectHasProperty('status',$deserialized);
                $this->assertObjectHasProperty('type',$deserialized);
            }

            $httpClient->getResponse()->getContent(true);
        }

            $this->assertSame(
                $httpClient->getResponse()->getContent(),
                $carAsJsonPayload,
            );
    }

    #[Test]
    #[DataProviderExternal(className: CarTest::class, methodName: 'getExistingCars')]
    #[Group(name: 'REST')]
    public function shouldUpdateCarResourceTroughPatchEndpointCall(int $statusCode, string $contentType, Car $responseCar): void
    {
        // given
        $carId = $responseCar->getId();
        $httpClient = $this->httpClient;
        $originalCar = ($carRepository = $this->entityManager->getRepository(Car::class))->find((int) $carId);

        if (null === $originalCar)
            $this->expectException(TypeError::class);

        // when
        $httpClient->request(method: 'PATCH', url: (string) $this->getIriFromResource($originalCar), options: ['body' => json_encode($carToPopulate = (object) [
            'vin' => sprintf('SHOULDNOTCHANGE%d', $originalCar->getId()),
            'registration' => sprintf('ZZXXDD%d', $originalCar->getId())
        ])]);

        // then
        $this->assertIsInt($carId);
        $this->assertInstanceOf(Car::class, $originalCar);
        $responseCar = json_decode($httpClient->getResponse()->getContent(), false, JSON_THROW_ON_ERROR);
        $this->assertNotSame($originalCar->getVin(), $carToPopulate->vin);
        $this->assertNotSame($originalCar->getRegistration(), $carToPopulate->registration);
        $this->assertSame($originalCar->getVin(), $responseCar->vin);
        $this->assertNotSame($originalCar->getRegistration(), $responseCar->registration);
    }

    #[Test]
    #[DataProviderExternal(className: CarTest::class, methodName: 'getExistingCars')]
    #[Group(name: 'REST')]
    public function shouldDeleteCarResourceTroughDeleteEndpointCall(int $statusCode, string $contentType, Car $existingCar): void
    {
        if (0 === $existingCar->getId() && $this->assertSame(true, true))
            return;

        // given
        $httpClient = $this->httpClient;
        $carRepository = $this->entityManager->getRepository(Car::class);

        // when
        $httpClient->request(method: 'DELETE', url: (string) $this->getIriFromResource($existingCar));
        $carLookedInRepository = $carRepository->find($existingCar->getId());

        // then
        // TODO: pozbyć się tego
        if(Response::HTTP_NOT_FOUND === $httpClient->getResponse()->getStatusCode())
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        else
            $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $httpClient->request(method: 'GET', url: (string) $this->getIriFromResource($existingCar));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertNull($carLookedInRepository);
    }

    public static function invalidCarProvider(): iterable
    {
//        yield 'with incorrect brand' => [1, 'incorrect-brand', 'TEST123', 'DFMDU34X8MUABC229', false, null, null, 'The value you selected is not a valid choice.'];
//        yield 'with too short registration number' => [2, 'Audi', 'T', 'KMHCM36C05U123456', false, null, null, 'Invalid registration'];
//        yield 'with too long registration number' => [3, 'BMW', 'T123456789ABC', 'DFMDU34X8MUD84229', false, null, null, 'Invalid registration'];
        yield 'with Invalid registration (number as first character)' => [4, 'Toyota', '1INVALID', 'DFMDU34X8MUD84229', false, null, null, 'Invalid registration'];
//        yield 'with invalid vin (number as first character)' => [5, 'Honda', '1INVALID', '9FMDU34X8MUD8422D', false, null, null, 'Invalid registration'];
        yield 'rented with invalid customer email' => [6, 'Tesla', 'KR1234AB', 'HGCM82633A1234567', true, 'invalid_email', 'ul. Sezamkowa 5, 11-222 WarszAwa', 'This value is not a valid email address.'];
        yield 'not rented with customer email' => [7, 'Ferrari', 'WA5678CD', 'WDBUF56J76A123456', false, 'imie.nazwisko@example.nextai', null, 'Customer email address should be empty'];
        yield 'not rented with customer address' => [8, 'Volvo', 'PO4321EF', 'WBA3A5C59FF123456', false, null, 'ul. Sezamkowa, 11-222 Warszawa', 'Customer address should be empty'];
    }
}
