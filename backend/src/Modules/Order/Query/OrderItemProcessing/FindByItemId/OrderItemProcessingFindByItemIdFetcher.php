<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItemProcessing\FindByItemId;

use App\Modules\Order\ReadModel\OrderItemProcessing\OrderItemProcessingByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderItemProcessingFindByItemIdFetcher
{
    private const string TABLE = 'order_item_processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<OrderItemProcessingByOrderId>
     * @throws Exception
     */
    public function fetch(OrderItemProcessingFindByItemIdQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'order_item_id', 'processing_id')
            ->from(self::TABLE)
            ->where('order_item_id = :itemId')
            ->setParameter('itemId', $query->itemId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderItemProcessingByOrderId> $items */
        $items = OrderItemProcessingByOrderId::fromRows($rows);

        return $items;
    }
}
