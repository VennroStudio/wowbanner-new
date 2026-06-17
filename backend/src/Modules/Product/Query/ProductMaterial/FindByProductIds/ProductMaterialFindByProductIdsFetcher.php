<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\ProductMaterial\FindByProductIds;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Product\ReadModel\ProductMaterial\Interface\ProductMaterialModelInterface;
use App\Modules\Product\ReadModel\ProductMaterial\ProductMaterialDetails;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductMaterialFindByProductIdsFetcher
{
    private const string TABLE = 'product_materials';
    private const string MATERIAL_TABLE = 'materials';
    private const string MATERIAL_OPTION_TABLE = 'material_options';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of ProductMaterialModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        ProductMaterialFindByProductIdsQuery $query,
        string $modelClass = ProductMaterialDetails::class,
    ): array {
        if ($query->productIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields(), 'pm'))
            ->from(self::TABLE, 'pm')
            ->innerJoin('pm', self::MATERIAL_TABLE, 'm', 'm.id = pm.material_id')
            ->innerJoin('pm', self::MATERIAL_OPTION_TABLE, 'mo', 'mo.id = pm.material_option_id')
            ->where('pm.product_id IN (:ids)')
            ->setParameter('ids', $query->productIds, ArrayParameterType::INTEGER)
            ->orderBy('pm.id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
