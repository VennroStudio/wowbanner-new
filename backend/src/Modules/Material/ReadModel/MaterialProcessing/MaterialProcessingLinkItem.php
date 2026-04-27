<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialProcessing;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class MaterialProcessingLinkItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.material_processing_id_required')]
        #[Assert\Positive]
        public int $processingId,
    ) {}
}
