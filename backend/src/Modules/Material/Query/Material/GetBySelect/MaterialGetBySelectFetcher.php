<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\Material\GetBySelect;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\Material\Interface\MaterialModelInterface;
use App\Modules\Material\ReadModel\Material\MaterialIdName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialGetBySelectFetcher
{
    private const string TABLE = 'materials';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of MaterialModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(MaterialGetBySelectQuery $query, string $modelClass = MaterialIdName::class): array
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
