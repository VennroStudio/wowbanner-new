<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialProcessing\GetBySelect;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class MaterialProcessingGetBySelectQuery
{
    public function __construct(
        #[Assert\Positive]
        public int $materialId,
        #[Assert\Positive]
        public int $optionId,
    ) {}
}
