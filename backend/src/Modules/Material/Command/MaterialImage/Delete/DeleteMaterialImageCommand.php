<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteMaterialImageCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $materialImageId,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
    ) {}
}
