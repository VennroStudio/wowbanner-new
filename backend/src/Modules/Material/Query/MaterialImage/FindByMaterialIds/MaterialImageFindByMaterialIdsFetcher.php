<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialImage\FindByMaterialIds;

use App\Modules\Material\ReadModel\MaterialImage\MaterialImageByMaterial;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialImageFindByMaterialIdsFetcher
{
    private const string TABLE = 'material_images';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<MaterialImageByMaterial>
     * @throws Exception
     */
    public function fetch(MaterialImageFindByMaterialIdsQuery $query): array
    {
        if ($query->materialIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'material_id', 'path', 'alt')
            ->from(self::TABLE)
            ->where('material_id IN (:ids)')
            ->setParameter('ids', $query->materialIds, ArrayParameterType::INTEGER)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return MaterialImageByMaterial::fromRows($rows);
    }
}
