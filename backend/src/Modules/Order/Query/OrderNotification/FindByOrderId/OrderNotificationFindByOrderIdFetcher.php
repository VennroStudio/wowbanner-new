<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderNotification\FindByOrderId;

use App\Modules\Order\ReadModel\OrderNotification\OrderNotificationByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderNotificationFindByOrderIdFetcher
{
    private const string TABLE = 'order_notifications';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<OrderNotificationByOrderId>
     * @throws Exception
     */
    public function fetch(OrderNotificationFindByOrderIdQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'order_id', 'notification_type', 'created_at')
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderNotificationByOrderId> $items */
        $items = OrderNotificationByOrderId::fromRows($rows);

        return $items;
    }
}
