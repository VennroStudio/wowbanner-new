<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\Material\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Modules\Material\ReadModel\Material\MaterialFindAll;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialFindAllFetcher
{
    private const string TABLE = 'materials';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return ModelCountItemsResult<MaterialFindAll>
     * @throws Exception
     */
    public function fetch(MaterialFindAllQuery $query): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE);

        if ($query->search !== null && $query->search !== '') {
            $qb->andWhere('name LIKE :search')
                ->setParameter('search', '%' . $query->search . '%');
        }

        $countQb = clone $qb;
        $total = (int)$countQb->select('COUNT(id)')->executeQuery()->fetchOne();

        $rows = $qb->select('id', 'name', 'description')
            ->orderBy('id', 'DESC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var list<MaterialFindAll> $items */
        $items = MaterialFindAll::fromRows($rows);

        return new ModelCountItemsResult(
            items: $items,
            count: $total,
        );
    }
}
