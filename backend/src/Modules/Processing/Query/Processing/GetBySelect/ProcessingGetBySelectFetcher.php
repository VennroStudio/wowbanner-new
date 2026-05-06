<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\Processing\GetBySelect;

use App\Modules\Processing\ReadModel\Processing\ProcessingGetBySelect;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingGetBySelectFetcher
{
    private const string TABLE = 'processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<ProcessingGetBySelect>
     * @throws Exception
     */
    public function fetch(ProcessingGetBySelectQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from(self::TABLE)
            ->orderBy('name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return ProcessingGetBySelect::fromRows($rows);
    }
}
