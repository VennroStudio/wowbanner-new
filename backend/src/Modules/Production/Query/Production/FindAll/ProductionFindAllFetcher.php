<?php

declare(strict_types=1);

namespace App\Modules\Production\Query\Production\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Modules\Production\ReadModel\Production\ProductionFindAll;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductionFindAllFetcher
{
    private const string TABLE = 'productions';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return ModelCountItemsResult<ProductionFindAll>
     * @throws Exception
     */
    public function fetch(ProductionFindAllQuery $query): ModelCountItemsResult
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

        /** @var list<ProductionFindAll> $items */
        $items = ProductionFindAll::fromRows($rows);

        return new ModelCountItemsResult(
            items: $items,
            count: $total,
        );
    }
}
