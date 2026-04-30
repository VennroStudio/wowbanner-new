<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItemProcessing\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderItemProcessing\OrderItemProcessing;
use App\Modules\Order\Entity\OrderItemProcessing\OrderItemProcessingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderItemProcessingRepository implements OrderItemProcessingRepository
{
    /** @var EntityRepository<OrderItemProcessing> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderItemProcessing::class);
    }

    #[Override]
    public function getById(int $id): OrderItemProcessing
    {
        $orderItemProcessing = $this->findById($id);
        if ($orderItemProcessing === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_item_processing_not_found',
                code: 1
            );
        }

        return $orderItemProcessing;
    }

    #[Override]
    public function findById(int $id): ?OrderItemProcessing
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<OrderItemProcessing>
     */
    #[Override]
    public function findByOrderItemId(int $orderItemId): array
    {
        return $this->repo->findBy(['orderItemId' => $orderItemId]);
    }

    #[Override]
    public function add(OrderItemProcessing $orderItemProcessing): void
    {
        $this->em->persist($orderItemProcessing);
    }

    #[Override]
    public function remove(OrderItemProcessing $orderItemProcessing): void
    {
        $this->em->remove($orderItemProcessing);
    }
}
