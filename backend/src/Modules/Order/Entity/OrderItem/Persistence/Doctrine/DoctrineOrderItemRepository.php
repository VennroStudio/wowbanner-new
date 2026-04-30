<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItem\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderItem\OrderItem;
use App\Modules\Order\Entity\OrderItem\OrderItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderItemRepository implements OrderItemRepository
{
    /** @var EntityRepository<OrderItem> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderItem::class);
    }

    #[Override]
    public function getById(int $id): OrderItem
    {
        $orderItem = $this->findById($id);
        if ($orderItem === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_item_not_found',
                code: 1
            );
        }

        return $orderItem;
    }

    #[Override]
    public function findById(int $id): ?OrderItem
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<OrderItem>
     */
    #[Override]
    public function findByOrderId(int $orderId): array
    {
        return $this->repo->findBy(['orderId' => $orderId]);
    }

    #[Override]
    public function add(OrderItem $orderItem): void
    {
        $this->em->persist($orderItem);
    }

    #[Override]
    public function remove(OrderItem $orderItem): void
    {
        $this->em->remove($orderItem);
    }
}
