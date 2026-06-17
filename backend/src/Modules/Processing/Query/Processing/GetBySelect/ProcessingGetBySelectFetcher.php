<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\Processing\GetBySelect;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Processing\ReadModel\Processing\Interface\ProcessingModelInterface;
use App\Modules\Processing\ReadModel\Processing\ProcessingIdName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingGetBySelectFetcher
{
    private const string TABLE = 'processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of ProcessingModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(ProcessingGetBySelectQuery $query, string $modelClass = ProcessingIdName::class): array
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
