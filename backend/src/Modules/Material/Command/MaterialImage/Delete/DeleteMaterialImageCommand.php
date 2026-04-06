<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteMaterialImageCommand
{
    public function __construct(
        public ?int $id = null,
        public ?int $materialId = null,
    ) {}
}
