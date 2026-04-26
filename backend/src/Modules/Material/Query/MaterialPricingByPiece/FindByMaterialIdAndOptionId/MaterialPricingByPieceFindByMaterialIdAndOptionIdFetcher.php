<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialPricingByPiece\FindByMaterialIdAndOptionId;

use App\Components\Cacher\Cacher;
use App\Modules\Material\ReadModel\MaterialPricingByPiece\MaterialPricingByPieceByMaterialIdAndOptionId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialPricingByPieceFindByMaterialIdAndOptionIdFetcher
{
    private const string TABLE = 'material_pricing_by_piece';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @return list<MaterialPricingByPieceByMaterialIdAndOptionId>
     * @throws Exception
     */
    public function fetch(MaterialPricingByPieceFindByMaterialIdAndOptionIdQuery $query): array
    {
        $key = 'material_pricing_by_piece_by_material_id_' . $query->materialId . '_option_id_' . $query->optionId;

        /** @var list<MaterialPricingByPieceByMaterialIdAndOptionId>|null $cached */
        $cached = $this->cacher->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'material_id',
                'option_id',
                'variant_type',
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

        $result = MaterialPricingByPieceByMaterialIdAndOptionId::fromRows($rows);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
