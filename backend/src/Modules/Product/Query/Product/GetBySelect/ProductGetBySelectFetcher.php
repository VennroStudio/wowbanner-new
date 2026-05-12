<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\Product\GetBySelect;

use App\Modules\Product\ReadModel\Product\ProductGetBySelect;
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
     * @return list<ProductGetBySelect>
     * @throws Exception
     */
    public function fetch(ProductGetBySelectQuery $query): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('p.id', 'p.name')
            ->from(self::TABLE, 'p')
            ->orderBy('p.name', 'ASC');

        if ($query->printId !== null) {
            $qb
                ->innerJoin('p', self::PRODUCT_PRINTS_TABLE, 'pp', 'pp.product_id = p.id')
                ->andWhere('pp.print_id = :printId')
                ->setParameter('printId', $query->printId)
                ->groupBy('p.id')
                ->addGroupBy('p.name');
        }

        $rows = $qb->executeQuery()->fetchAllAssociative();

        return ProductGetBySelect::fromRows($rows);
    }
}
