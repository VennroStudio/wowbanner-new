<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItemMilling\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderItemMilling\OrderItemMilling;
use App\Modules\Order\Entity\OrderItemMilling\OrderItemMillingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderItemMillingRepository implements OrderItemMillingRepository
{
    /** @var EntityRepository<OrderItemMilling> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderItemMilling::class);
    }

    #[Override]
    public function getById(int $id): OrderItemMilling
    {
        $orderItemMilling = $this->findById($id);
        if ($orderItemMilling === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_item_milling_not_found',
                code: 1
            );
        }

        return $orderItemMilling;
    }

    #[Override]
    public function findById(int $id): ?OrderItemMilling
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<OrderItemMilling>
     */
    #[Override]
    public function findByOrderId(int $orderId): array
    {
        return $this->repo->findBy(['orderId' => $orderId]);
    }

    #[Override]
    public function add(OrderItemMilling $orderItemMilling): void
    {
        $this->em->persist($orderItemMilling);
    }

    #[Override]
    public function remove(OrderItemMilling $orderItemMilling): void
    {
        $this->em->remove($orderItemMilling);
    }
}
