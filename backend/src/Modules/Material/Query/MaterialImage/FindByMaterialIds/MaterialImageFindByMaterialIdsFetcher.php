<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialImage\FindByMaterialIds;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\MaterialImage\Interface\MaterialImageModelInterface;
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
     * @template T of MaterialImageModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        MaterialImageFindByMaterialIdsQuery $query,
        string $modelClass = MaterialImageByMaterial::class,
    ): array
    {
        if ($query->materialIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('material_id IN (:ids)')
            ->setParameter('ids', $query->materialIds, ArrayParameterType::INTEGER)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
