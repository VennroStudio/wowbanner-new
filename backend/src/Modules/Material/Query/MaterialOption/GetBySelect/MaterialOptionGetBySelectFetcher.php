<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\GetBySelect;

use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionGetBySelect;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialOptionGetBySelectFetcher
{
    private const string TABLE = 'material_options';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<MaterialOptionGetBySelect>
     * @throws Exception
     */
    public function fetch(MaterialOptionGetBySelectQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'material_id')
            ->from(self::TABLE)
            ->where('material_id = :materialId')
            ->setParameter('materialId', $query->materialId)
            ->orderBy('name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return MaterialOptionGetBySelect::fromRows($rows);
    }
}
