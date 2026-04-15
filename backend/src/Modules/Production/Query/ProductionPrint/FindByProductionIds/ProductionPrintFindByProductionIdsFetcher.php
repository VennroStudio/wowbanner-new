<?php

declare(strict_types=1);

namespace App\Modules\Production\Query\ProductionPrint\FindByProductionIds;

use App\Modules\Production\ReadModel\ProductionPrint\ProductionPrintByProductionId;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductionPrintFindByProductionIdsFetcher
{
    private const string TABLE = 'production_prints';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<ProductionPrintByProductionId>
     * @throws Exception
     */
    public function fetch(ProductionPrintFindByProductionIdsQuery $query): array
    {
        if ($query->productionIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'production_id', 'print_id')
            ->from(self::TABLE)
            ->where('production_id IN (:ids)')
            ->setParameter('ids', $query->productionIds, ArrayParameterType::INTEGER)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ProductionPrintByProductionId> $items */
        $items = ProductionPrintByProductionId::fromRows($rows);

        return $items;
    }
}
