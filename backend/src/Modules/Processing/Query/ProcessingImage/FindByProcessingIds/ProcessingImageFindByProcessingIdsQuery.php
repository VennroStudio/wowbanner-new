<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\ProcessingImage\FindByProcessingIds;

final readonly class ProcessingImageFindByProcessingIdsQuery
{
    /** @param list<int> $processingIds */
    public function __construct(
        public array $processingIds,
    ) {}
}
