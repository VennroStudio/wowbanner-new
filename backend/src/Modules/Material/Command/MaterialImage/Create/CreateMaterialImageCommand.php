<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Create;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateMaterialImageCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_id_required')]
        public int $materialId,
        #[Assert\NotBlank(message: 'validation.material_image_path_required')]
        public string $tmpFilePath,
        public ?string $alt = null,
    ) {}
}
