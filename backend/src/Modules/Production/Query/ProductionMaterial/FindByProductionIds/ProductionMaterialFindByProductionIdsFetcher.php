<?php

declare(strict_types=1);

namespace App\Modules\Production\Query\ProductionMaterial\FindByProductionIds;

use App\Modules\Production\ReadModel\ProductionMaterial\ProductionMaterialByProductionId;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductionMaterialFindByProductionIdsFetcher
{
    private const string TABLE = 'production_materials';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<ProductionMaterialByProductionId>
     * @throws Exception
     */
    public function fetch(ProductionMaterialFindByProductionIdsQuery $query): array
    {
        if ($query->productionIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'production_id', 'material_option_id')
            ->from(self::TABLE)
            ->where('production_id IN (:ids)')
            ->setParameter('ids', $query->productionIds, ArrayParameterType::INTEGER)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ProductionMaterialByProductionId> $items */
        $items = ProductionMaterialByProductionId::fromRows($rows);

        return $items;
    }
}
