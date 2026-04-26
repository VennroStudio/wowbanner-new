<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\Product\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Modules\Product\ReadModel\Product\ProductFindAll;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductFindAllFetcher
{
    private const string TABLE = 'products';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return ModelCountItemsResult<ProductFindAll>
     * @throws Exception
     */
    public function fetch(ProductFindAllQuery $query): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE);

        if ($query->search !== null && $query->search !== '') {
            $qb->andWhere('LOWER(name) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $query->search . '%');
        }

        $countQb = clone $qb;
        $total = (int)$countQb->select('COUNT(id)')->executeQuery()->fetchOne();

        $rows = $qb->select('id', 'name')
            ->orderBy('id', 'DESC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<ProductFindAll> $items */
        $items = ProductFindAll::fromRows($rows);

        return new ModelCountItemsResult(
            items: $items,
            count: $total,
        );
    }
}
