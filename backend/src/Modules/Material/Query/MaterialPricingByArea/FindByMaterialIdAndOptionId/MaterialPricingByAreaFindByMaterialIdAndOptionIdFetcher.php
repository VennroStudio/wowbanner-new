<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialPricingByArea\FindByMaterialIdAndOptionId;

use App\Components\Cacher\Cacher;
use App\Modules\Material\ReadModel\MaterialPricingByArea\MaterialPricingByAreaByMaterialIdAndOptionId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialPricingByAreaFindByMaterialIdAndOptionIdFetcher
{
    private const string TABLE = 'material_pricing_by_area';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @return list<MaterialPricingByAreaByMaterialIdAndOptionId>
     * @throws Exception
     */
    public function fetch(MaterialPricingByAreaFindByMaterialIdAndOptionIdQuery $query): array
    {
        $key = 'material_pricing_by_area_by_material_id_' . $query->materialId . '_option_id_' . $query->optionId;

        /** @var list<MaterialPricingByAreaByMaterialIdAndOptionId>|null $cached */
        $cached = $this->cacher->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'material_id',
                'option_id',
                'dpi_type',
                'area_range_type',
                'price',
                'cost',
                'print_hours'
            )
            ->from(self::TABLE)
            ->where('material_id = :materialId')
            ->andWhere('option_id = :optionId')
            ->setParameter('materialId', $query->materialId)
            ->setParameter('optionId', $query->optionId)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = MaterialPricingByAreaByMaterialIdAndOptionId::fromRows($rows);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
