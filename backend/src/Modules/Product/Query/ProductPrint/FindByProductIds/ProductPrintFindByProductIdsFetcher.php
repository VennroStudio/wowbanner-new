<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\ProductPrint\FindByProductIds;

use App\Components\ReadModel\ReadModelFields;
use App\Modules\Product\ReadModel\ProductPrint\Interface\ProductPrintModelInterface;
use App\Modules\Product\ReadModel\ProductPrint\ProductPrintDetails;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProductPrintFindByProductIdsFetcher
{
    private const string TABLE = 'product_prints';
    private const string PRINTING_TABLE = 'printings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @template T of ProductPrintModelInterface
     * @param class-string<T> $modelClass
     * @return list<T>
     * @throws Exception
     */
    public function fetch(
        ProductPrintFindByProductIdsQuery $query,
        string $modelClass = ProductPrintDetails::class,
    ): array {
        if ($query->productIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select(...ReadModelFields::select($modelClass::fields(), 'pp'))
            ->from(self::TABLE, 'pp')
            ->innerJoin('pp', self::PRINTING_TABLE, 'p', 'p.id = pp.print_id')
            ->where('pp.product_id IN (:ids)')
            ->setParameter('ids', $query->productIds, ArrayParameterType::INTEGER)
            ->orderBy('pp.id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $modelClass::fromRows($rows);
    }
}
