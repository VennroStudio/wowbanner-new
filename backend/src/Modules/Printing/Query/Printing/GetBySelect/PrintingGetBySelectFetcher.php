<?php

declare(strict_types=1);

namespace App\Modules\Printing\Query\Printing\GetBySelect;

use App\Modules\Printing\ReadModel\Printing\PrintingGetBySelect;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class PrintingGetBySelectFetcher
{
    private const string TABLE = 'printings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<PrintingGetBySelect>
     * @throws Exception
     */
    public function fetch(PrintingGetBySelectQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from(self::TABLE)
            ->orderBy('name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return PrintingGetBySelect::fromRows($rows);
    }
}
