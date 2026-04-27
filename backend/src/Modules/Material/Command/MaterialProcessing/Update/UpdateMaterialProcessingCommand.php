<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialProcessing\Update;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateMaterialProcessingCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_processing_link_id_required')]
        #[Assert\Positive]
        public int $id,

        #[Assert\NotBlank(message: 'validation.material_id_required')]
        #[Assert\Positive]
        public int $materialId,

        #[Assert\NotBlank(message: 'validation.material_option_id_required')]
        #[Assert\Positive]
        public int $optionId,

        #[Assert\NotBlank(message: 'validation.material_processing_id_required')]
        #[Assert\Positive]
        public int $processingId,
    ) {}
}
