<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\Material\GetBySelect;

use App\Modules\Material\ReadModel\Material\MaterialGetBySelect;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialGetBySelectFetcher
{
    private const string TABLE = 'materials';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<MaterialGetBySelect>
     * @throws Exception
     */
    public function fetch(MaterialGetBySelectQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from(self::TABLE)
            ->orderBy('name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return MaterialGetBySelect::fromRows($rows);
    }
}
