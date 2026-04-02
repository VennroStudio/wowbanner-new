<?php

declare(strict_types=1);

namespace App\Modules\Printing\Command\Printing\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeletePrintingCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $printingId,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
    ) {}
}
