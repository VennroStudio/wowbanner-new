<?php

declare(strict_types=1);

namespace App\Modules\Printing\Query\Printing\FindAll;

use App\Components\ReadModel\ModelCountItemsResult;
use App\Components\ReadModel\ReadModelFields;
use App\Modules\Printing\ReadModel\Printing\Interface\PrintingModelInterface;
use App\Modules\Printing\ReadModel\Printing\PrintingIdName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class PrintingFindAllFetcher
{
    private const string TABLE = 'printings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of PrintingModelInterface
     * @param class-string<T> $modelClass
     * @return ModelCountItemsResult<T>
     * @throws Exception
     */
    public function fetch(PrintingFindAllQuery $query, string $modelClass = PrintingIdName::class): ModelCountItemsResult
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
