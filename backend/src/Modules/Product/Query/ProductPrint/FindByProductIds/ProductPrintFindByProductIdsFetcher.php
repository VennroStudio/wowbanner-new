<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\ProductPrint\FindByProductIds;

use App\Modules\Product\ReadModel\ProductPrint\ProductPrintByProductId;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductPrintFindByProductIdsFetcher
{
    private const string TABLE = 'product_prints';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<ProductPrintByProductId>
     * @throws Exception
     */
    public function fetch(ProductPrintFindByProductIdsQuery $query): array
    {
        if ($query->productIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'product_id', 'print_id')
            ->from(self::TABLE)
            ->where('product_id IN (:ids)')
            ->setParameter('ids', $query->productIds, ArrayParameterType::INTEGER)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ProductPrintByProductId> $items */
        $items = ProductPrintByProductId::fromRows($rows);

        return $items;
    }
}
