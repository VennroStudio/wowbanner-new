<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\ProcessingImage\FindByProcessing;

use App\Modules\Processing\ReadModel\ProcessingImage\ProcessingImageByProcessing;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class ProcessingImageFindByProcessingFetcher
{
    private const string TABLE = 'processing_images';

    public function __construct(
        private Connection $connection,
    ) {}

    /**
     * @return ProcessingImageByProcessing[]
     * @throws Exception
     */
    public function fetch(ProcessingImageFindByProcessingQuery $query): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('id', 'processing_id', 'path', 'alt')
            ->from(self::TABLE)
            ->where('processing_id = :processingId')
            ->setParameter('processingId', $query->processingId)
            ->executeQuery()
            ->fetchAllAssociative();

        return ProcessingImageByProcessing::fromRows($rows);
    }
}
