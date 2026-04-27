<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialProcessing\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteMaterialProcessingCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_processing_link_id_required')]
        #[Assert\Positive]
        public int $id,
    ) {}
}
