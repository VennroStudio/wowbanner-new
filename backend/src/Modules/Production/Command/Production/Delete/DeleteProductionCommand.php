<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\Production\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteProductionCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,

        #[Assert\NotBlank]
        public int $currentUserRole,

        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $id,
    ) {}
}
