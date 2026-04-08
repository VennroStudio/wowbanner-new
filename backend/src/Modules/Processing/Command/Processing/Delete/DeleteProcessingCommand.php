<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\Processing\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteProcessingCommand
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
