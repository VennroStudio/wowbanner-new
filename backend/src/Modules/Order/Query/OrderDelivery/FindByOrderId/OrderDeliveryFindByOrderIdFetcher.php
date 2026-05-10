<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderDelivery\FindByOrderId;

use App\Modules\Order\ReadModel\OrderDelivery\OrderDeliveryByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderDeliveryFindByOrderIdFetcher
{
    private const string TABLE = 'order_deliveries';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch(OrderDeliveryFindByOrderIdQuery $query): ?OrderDeliveryByOrderId
    {
        $row = $this->connection->createQueryBuilder()
            ->select('id', 'order_id', 'delivery_type', 'address', 'comment')
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        /**
         * @var array{
         *     id: int,
         *     order_id: int,
         *     delivery_type: int,
         *     address: string|null,
         *     comment: string|null
         * } $row
         */
        return OrderDeliveryByOrderId::fromRow($row);
    }
}
