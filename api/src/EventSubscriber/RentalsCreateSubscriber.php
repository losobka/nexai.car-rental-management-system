<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Address;
use App\Entity\Car;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class RentalsCreateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::FINISH_REQUEST => ['persistAddress', EventPriorities::POST_WRITE],
        ];
    }

    public function persistAddress(FinishRequestEvent $event): void
    {
        if (
            '_api_/cars/{carId}/rentals_post' !== $event->getRequest()->attributes->get('_api_operation_name')
        )
            return;

        if (
            null === ($billingAddress = $this->entityManager->getRepository(Car::class)->find($event->getRequest()->attributes->get('carId'))?->getLatestRental()?->getBillingAddress())
            || 0 !== count($data = $this->entityManager->getRepository(Address::class)->findBy([
                'city' => (string) $billingAddress->getCity(),
                'street' => (string)  $billingAddress->getStreet(),
                'postalCode' => (string) $billingAddress->getPostalCode()
            ]))
        )
            return;

        $address = new Address;
        $address->setPostalCode((string) $billingAddress->getPostalCode());
        $address->setStreet((string) $billingAddress->getStreet());
        $address->setCity((string) $billingAddress->getCity());

        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }
}
