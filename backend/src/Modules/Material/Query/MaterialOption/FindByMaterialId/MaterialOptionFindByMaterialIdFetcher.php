<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\FindByMaterialId;

use App\Components\Cacher\Cacher;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionByMaterialId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialOptionFindByMaterialIdFetcher
{
    private const string TABLE = 'material_options';
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @return list<MaterialOptionByMaterialId>
     * @throws Exception
     */
    public function fetch(MaterialOptionFindByMaterialIdQuery $query): array
    {
        $key = 'material_option_by_material_id_' . $query->materialId;

        /** @var list<MaterialOptionByMaterialId>|null $cached */
        $cached = $this->cacher->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'material_id', 'pricing_type', 'is_cut')
            ->from(self::TABLE)
            ->where('material_id = :materialId')
            ->setParameter('materialId', $query->materialId)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = MaterialOptionByMaterialId::fromRows($rows);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
