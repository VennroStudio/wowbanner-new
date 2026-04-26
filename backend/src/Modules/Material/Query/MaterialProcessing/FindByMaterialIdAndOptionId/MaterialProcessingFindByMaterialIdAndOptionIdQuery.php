<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialProcessing\FindByMaterialIdAndOptionId;

final readonly class MaterialProcessingFindByMaterialIdAndOptionIdQuery
{
    public function __construct(
        public int $materialId,
        public int $optionId,
    ) {}
}
