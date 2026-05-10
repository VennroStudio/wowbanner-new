<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderService\FindByOrderId;

use App\Modules\Order\ReadModel\OrderService\OrderServiceByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class OrderServiceFindByOrderIdFetcher
{
    private const string TABLE = 'order_services';
    public const string ALIAS = 'os';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.order_id = ' . $alias . '.id');
    }

    /**
     * @return list<OrderServiceByOrderId>
     * @throws Exception
     */
    public function fetch(OrderServiceFindByOrderIdQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'order_id', 'service_type', 'price', 'note')
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderServiceByOrderId> $items */
        $items = OrderServiceByOrderId::fromRows($rows);

        return $items;
    }
}
