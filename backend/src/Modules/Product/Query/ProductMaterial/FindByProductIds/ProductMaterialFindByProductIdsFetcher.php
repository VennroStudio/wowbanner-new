<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\ProductMaterial\FindByProductIds;

use App\Modules\Product\ReadModel\ProductMaterial\ProductMaterialByProductId;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductMaterialFindByProductIdsFetcher
{
    private const string TABLE = 'Product_materials';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<ProductMaterialByProductId>
     * @throws Exception
     */
    public function fetch(ProductMaterialFindByProductIdsQuery $query): array
    {
        if ($query->ProductIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'Product_id', 'material_option_id')
            ->from(self::TABLE)
            ->where('Product_id IN (:ids)')
            ->setParameter('ids', $query->ProductIds, ArrayParameterType::INTEGER)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ProductMaterialByProductId> $items */
        $items = ProductMaterialByProductId::fromRows($rows);

        return $items;
    }
}
