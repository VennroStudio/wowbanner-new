<?php

declare(strict_types=1);

namespace App\Modules\Printing\Query\Printing\GetBySelect;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Printing\ReadModel\Printing\Interface\PrintingModelInterface;
use App\Modules\Printing\ReadModel\Printing\PrintingIdName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class PrintingGetBySelectFetcher
{
    private const string TABLE = 'printings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of PrintingModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(PrintingGetBySelectQuery $query, string $modelClass = PrintingIdName::class): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->orderBy('name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
