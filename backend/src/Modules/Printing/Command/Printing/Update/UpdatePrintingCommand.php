<?php

declare(strict_types=1);

namespace App\Modules\Printing\Command\Printing\Update;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdatePrintingCommand
{
    private const int NAME_MIN_LENGTH = 2;
    private const int NAME_MAX_LENGTH = 255;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $printingId,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
        #[Assert\NotBlank(message: 'validation.printing_name_required')]
        #[Assert\Length(
            min: self::NAME_MIN_LENGTH,
            max: self::NAME_MAX_LENGTH,
            minMessage: 'validation.printing_name_too_short',
            maxMessage: 'validation.printing_name_too_long',
        )]
        public string $name,
    ) {}
}
