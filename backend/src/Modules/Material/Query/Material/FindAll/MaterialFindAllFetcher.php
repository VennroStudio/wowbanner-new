<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\Material\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Material\ReadModel\Material\Interface\MaterialModelInterface;
use App\Modules\Material\ReadModel\Material\MaterialDetails;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialFindAllFetcher
{
    private const string TABLE = 'materials';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of MaterialModelInterface
     * @param class-string<T> $modelClass
     * @return ModelCountItemsResult<T>
     * @throws Exception
     */
    public function fetch(MaterialFindAllQuery $query, string $modelClass = MaterialDetails::class): ModelCountItemsResult
    {
        $qb = $this->connection->createQueryBuilder()
            ->from(self::TABLE);

        if ($query->search !== null && $query->search !== '') {
            $qb->andWhere('name LIKE :search')
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
