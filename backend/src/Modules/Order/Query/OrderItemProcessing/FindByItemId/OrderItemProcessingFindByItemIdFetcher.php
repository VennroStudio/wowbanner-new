<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItemProcessing\FindByItemId;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Order\ReadModel\OrderItemProcessing\Interface\OrderItemProcessingModelInterface;
use App\Modules\Order\ReadModel\OrderItemProcessing\OrderItemProcessingDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class OrderItemProcessingFindByItemIdFetcher
{
    private const string TABLE = 'order_item_processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of OrderItemProcessingModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        OrderItemProcessingFindByItemIdQuery $query,
        string $modelClass = OrderItemProcessingDetails::class,
    ): array {
        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('order_item_id = :itemId')
            ->setParameter('itemId', $query->itemId)
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
