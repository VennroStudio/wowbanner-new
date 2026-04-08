<?php

declare(strict_types=1);

namespace App\Modules\Processing\Query\ProcessingImage\FindByProcessing;

final readonly class ProcessingImageFindByProcessingQuery
{
    public function __construct(
        public int $processingId,
    ) {}
}
