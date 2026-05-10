<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderFile\FindByOrderId;

use App\Modules\Order\ReadModel\OrderFile\OrderFileByOrderId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderFileFindByOrderIdFetcher
{
    private const string TABLE = 'order_files';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<OrderFileByOrderId>
     * @throws Exception
     */
    public function fetch(OrderFileFindByOrderIdQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'order_id', 'disk_path', 'file_name', 'original_name', 'created_at')
            ->from(self::TABLE)
            ->where('order_id = :orderId')
            ->setParameter('orderId', $query->orderId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<OrderFileByOrderId> $items */
        $items = OrderFileByOrderId::fromRows($rows);

        return $items;
    }
}
