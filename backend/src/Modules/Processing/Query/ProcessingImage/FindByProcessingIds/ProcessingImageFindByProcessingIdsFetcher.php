<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\ProcessingImage\FindByProcessingIds;

use App\Modules\Processing\ReadModel\ProcessingImage\ProcessingImageByProcessing;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingImageFindByProcessingIdsFetcher
{
    private const string TABLE = 'processing_images';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return list<ProcessingImageByProcessing>
     * @throws Exception
     */
    public function fetch(ProcessingImageFindByProcessingIdsQuery $query): array
    {
        if ($query->processingIds === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'processing_id', 'path', 'alt')
            ->from(self::TABLE)
            ->where('processing_id IN (:ids)')
            ->setParameter('ids', $query->processingIds, ArrayParameterType::INTEGER)
            ->orderBy('id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return ProcessingImageByProcessing::fromRows($rows);
    }
}
