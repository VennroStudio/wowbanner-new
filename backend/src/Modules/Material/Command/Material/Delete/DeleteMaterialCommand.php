<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteMaterialCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $materialId,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
    ) {}
}
