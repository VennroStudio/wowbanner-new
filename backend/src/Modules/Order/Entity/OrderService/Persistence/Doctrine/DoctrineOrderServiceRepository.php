<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderService\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderService\OrderService;
use App\Modules\Order\Entity\OrderService\OrderServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderServiceRepository implements OrderServiceRepository
{
    /** @var EntityRepository<OrderService> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderService::class);
    }

    #[Override]
    public function getById(int $id): OrderService
    {
        $orderService = $this->findById($id);
        if ($orderService === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_service_not_found',
                code: 1,
            );
        }

        return $orderService;
    }

    #[Override]
    public function findById(int $id): ?OrderService
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<OrderService>
     */
    #[Override]
    public function findByOrderId(int $orderId): array
    {
        return $this->repo->findBy(['orderId' => $orderId]);
    }

    #[Override]
    public function add(OrderService $orderService): void
    {
        $this->em->persist($orderService);
    }

    #[Override]
    public function remove(OrderService $orderService): void
    {
        $this->em->remove($orderService);
    }
}
