<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\Product\GetBySelect;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Product\ReadModel\Product\Interface\ProductModelInterface;
use App\Modules\Product\ReadModel\Product\ProductIdName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductGetBySelectFetcher
{
    private const string TABLE = 'products';
    private const string PRODUCT_PRINTS_TABLE = 'product_prints';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of ProductModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(ProductGetBySelectQuery $query, string $modelClass = ProductIdName::class): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields(), 'p'))
            ->from(self::TABLE, 'p')
            ->orderBy('p.name', 'ASC');

        if ($query->printId !== null) {
            $qb
                ->distinct()
                ->innerJoin('p', self::PRODUCT_PRINTS_TABLE, 'pp', 'pp.product_id = p.id')
                ->andWhere('pp.print_id = :printId')
                ->setParameter('printId', $query->printId);
        }

        $rows = $qb->executeQuery()->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
