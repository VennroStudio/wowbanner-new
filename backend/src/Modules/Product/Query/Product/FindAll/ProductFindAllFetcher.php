<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\Product\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Product\ReadModel\Product\Interface\ProductModelInterface;
use App\Modules\Product\ReadModel\Product\ProductIdName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductFindAllFetcher
{
    private const string TABLE = 'products';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of ProductModelInterface
     * @param class-string<T> $modelClass
     * @return ModelCountItemsResult<T>
     * @throws Exception
     */
    public function fetch(ProductFindAllQuery $query, string $modelClass = ProductIdName::class): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE);

        if ($query->search !== null && $query->search !== '') {
            $qb->andWhere('LOWER(name) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $query->search . '%');
        }

        $countQb = clone $qb;
        $total = (int)$countQb->select('COUNT(id)')->executeQuery()->fetchOne();

        $rows = $qb->select(...ReadModelFields::select($modelClass::fields()))
            ->orderBy('id', 'DESC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        $items = $modelClass::fromRows($rows);

        return new ModelCountItemsResult(
            items: $items,
            count: $total,
        );
    }
}
