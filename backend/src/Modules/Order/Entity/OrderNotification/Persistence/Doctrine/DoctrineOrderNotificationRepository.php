<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderNotification\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Order\Entity\OrderNotification\OrderNotification;
use App\Modules\Order\Entity\OrderNotification\OrderNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Override;

final readonly class DoctrineOrderNotificationRepository implements OrderNotificationRepository
{
    /** @var EntityRepository<OrderNotification> */
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(OrderNotification::class);
    }

    #[Override]
    public function getById(int $id): OrderNotification
    {
        $orderNotification = $this->findById($id);
        if ($orderNotification === null) {
            throw new DomainExceptionModule(
                module: 'order',
                message: 'error.order_notification_not_found',
                code: 1,
            );
        }

        return $orderNotification;
    }

    #[Override]
    public function findById(int $id): ?OrderNotification
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    /**
     * @return list<OrderNotification>
     */
    #[Override]
    public function findByOrderId(int $orderId): array
    {
        return $this->repo->findBy(['orderId' => $orderId]);
    }

    #[Override]
    public function add(OrderNotification $orderNotification): void
    {
        $this->em->persist($orderNotification);
    }

    #[Override]
    public function remove(OrderNotification $orderNotification): void
    {
        $this->em->remove($orderNotification);
    }
}
