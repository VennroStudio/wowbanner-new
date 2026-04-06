<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Update;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateMaterialImageCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_image_id_required')]
        public int $id,
        public ?string $alt = null,
    ) {}
}
