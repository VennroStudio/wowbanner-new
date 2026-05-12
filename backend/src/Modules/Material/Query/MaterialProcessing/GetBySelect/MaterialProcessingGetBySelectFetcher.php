<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialProcessing\GetBySelect;

use App\Modules\Material\ReadModel\MaterialProcessing\MaterialProcessingGetBySelect;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class MaterialProcessingGetBySelectFetcher
{
    private const string TABLE = 'material_processings';
    private const string PROCESSINGS_TABLE = 'processings';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<MaterialProcessingGetBySelect>
     * @throws Exception
     */
    public function fetch(MaterialProcessingGetBySelectQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('p.id', 'p.name')
            ->from(self::TABLE, 'mp')
            ->innerJoin('mp', self::PROCESSINGS_TABLE, 'p', 'p.id = mp.processing_id')
            ->where('mp.material_id = :materialId')
            ->andWhere('mp.option_id = :optionId')
            ->setParameter('materialId', $query->materialId)
            ->setParameter('optionId', $query->optionId)
            ->orderBy('p.name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return MaterialProcessingGetBySelect::fromRows($rows);
    }
}
