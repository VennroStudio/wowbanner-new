<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialPricingCut\FindByMaterialIdAndOptionId;

use App\Components\Cacher\Cacher;
use App\Modules\Material\ReadModel\MaterialPricingCut\MaterialPricingCutByMaterialIdAndOptionId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialPricingCutFindByMaterialIdAndOptionIdFetcher
{
    private const string TABLE = 'material_pricing_cuts';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @return list<MaterialPricingCutByMaterialIdAndOptionId>
     * @throws Exception
     */
    public function fetch(MaterialPricingCutFindByMaterialIdAndOptionIdQuery $query): array
    {
        $key = 'material_pricing_cut_by_material_id_' . $query->materialId . '_option_id_' . $query->optionId;

        /** @var list<MaterialPricingCutByMaterialIdAndOptionId>|null $cached */
        $cached = $this->cacher->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'material_id', 'option_id', 'type AS cut_type', 'price')
            ->from(self::TABLE)
            ->where('material_id = :materialId')
            ->andWhere('option_id = :optionId')
            ->setParameter('materialId', $query->materialId)
            ->setParameter('optionId', $query->optionId)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = MaterialPricingCutByMaterialIdAndOptionId::fromRows($rows);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
