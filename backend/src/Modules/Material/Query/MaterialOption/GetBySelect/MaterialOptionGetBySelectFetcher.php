<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\GetBySelect;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\MaterialOption\Interface\MaterialOptionModelInterface;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionIdName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialOptionGetBySelectFetcher
{
    private const string TABLE = 'material_options';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of MaterialOptionModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        MaterialOptionGetBySelectQuery $query,
        string $modelClass = MaterialOptionIdName::class,
    ): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields()))
            ->from(self::TABLE)
            ->where('material_id = :materialId')
            ->setParameter('materialId', $query->materialId)
            ->orderBy('name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
