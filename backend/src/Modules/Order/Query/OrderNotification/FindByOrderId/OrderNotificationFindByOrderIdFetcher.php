<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderNotification\FindByOrderId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderNotification\Interface\OrderNotificationModelInterface;
use App\Modules\Order\ReadModel\OrderNotification\OrderNotificationDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderNotificationFindByOrderIdFetcher
{
    private const string TABLE = 'order_notifications';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of OrderNotificationModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        OrderNotificationFindByOrderIdQuery $query,
        string $modelClass = OrderNotificationDetails::class,
    ): array {
        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
