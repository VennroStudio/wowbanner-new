<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderPayment\FindByOrderId;

use App\Modules\Order\ReadModel\OrderPayment\OrderPaymentByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderPaymentFindByOrderIdFetcher
{
    private const string TABLE = 'order_payments';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<OrderPaymentByOrderId>
     * @throws Exception
     */
    public function fetch(OrderPaymentFindByOrderIdQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'order_id',
                'client_id',
                'operation_type',
                'payment_type',
                'reason',
                'note',
                'confirmation',
                'created_at'
            )
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderPaymentByOrderId> $items */
        $items = OrderPaymentByOrderId::fromRows($rows);

        return $items;
    }
}
