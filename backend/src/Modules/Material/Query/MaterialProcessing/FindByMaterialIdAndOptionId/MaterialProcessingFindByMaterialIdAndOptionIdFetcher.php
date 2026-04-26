<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialProcessing\FindByMaterialIdAndOptionId;

use App\Components\Cacher\Cacher;
use App\Modules\Material\ReadModel\MaterialProcessing\MaterialProcessingByMaterialIdAndOptionId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialProcessingFindByMaterialIdAndOptionIdFetcher
{
    private const string TABLE = 'material_processings';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @return list<MaterialProcessingByMaterialIdAndOptionId>
     * @throws Exception
     */
    public function fetch(MaterialProcessingFindByMaterialIdAndOptionIdQuery $query): array
    {
        $key = 'material_processing_by_material_id_' . $query->materialId . '_option_id_' . $query->optionId;

        /** @var list<MaterialProcessingByMaterialIdAndOptionId>|null $cached */
        $cached = $this->cacher->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'material_id', 'option_id', 'processing_id')
            ->from(self::TABLE)
            ->where('material_id = :materialId')
            ->andWhere('option_id = :optionId')
            ->setParameter('materialId', $query->materialId)
            ->setParameter('optionId', $query->optionId)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = MaterialProcessingByMaterialIdAndOptionId::fromRows($rows);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
