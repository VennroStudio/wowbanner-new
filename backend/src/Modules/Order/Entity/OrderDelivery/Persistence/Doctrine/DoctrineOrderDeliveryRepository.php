<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderDelivery\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderDelivery\OrderDelivery;
use App\Modules\Order\Entity\OrderDelivery\OrderDeliveryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderDeliveryRepository implements OrderDeliveryRepository
{
    /** @var EntityRepository<OrderDelivery> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderDelivery::class);
    }

    #[Override]
    public function getById(int $id): OrderDelivery
    {
        $orderDelivery = $this->findById($id);
        if ($orderDelivery === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_delivery_not_found',
                code: 1
            );
        }

        return $orderDelivery;
    }

    #[Override]
    public function findById(int $id): ?OrderDelivery
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    #[Override]
    public function findByOrderId(int $orderId): ?OrderDelivery
    {
        return $this->repo->findOneBy(['orderId' => $orderId]);
    }

    #[Override]
    public function add(OrderDelivery $orderDelivery): void
    {
        $this->em->persist($orderDelivery);
    }

    #[Override]
    public function remove(OrderDelivery $orderDelivery): void
    {
        $this->em->remove($orderDelivery);
    }
}
