<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItem\FindByOrderId;

use App\Modules\Order\ReadModel\OrderItem\OrderItemByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class OrderItemFindByOrderIdFetcher
{
    private const string TABLE = 'order_items';
    public const string ALIAS = 'oi';

    public function __construct(
        private Connection $connection,
    ) {}

    public function joinForFilter(QueryBuilder $qb, string $alias): void
    {
        $qb->leftJoin($alias, self::TABLE, self::ALIAS, self::ALIAS . '.order_id = ' . $alias . '.id');
    }

    /**
     * @return list<OrderItemByOrderId>
     * @throws Exception
     */
    public function fetch(OrderItemFindByOrderIdQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'order_id',
                'source_item_id',
                'print_id',
                'product_id',
                'material_id',
                'option_id',
                'dpi_type',
                'variant_type',
                'width',
                'height',
                'quantity',
                'performer_id',
                'note',
                'printed',
                'ready',
                'price'
            )
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderItemByOrderId> $items */
        $items = OrderItemByOrderId::fromRows($rows);

        return $items;
    }
}
