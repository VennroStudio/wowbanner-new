<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialOption\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteMaterialOptionCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_option_id_required')]
        #[Assert\Positive]
        public int $id,
    ) {}
}
