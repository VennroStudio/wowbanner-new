<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\ProcessingImage\Update;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProcessingImageCommand
{
    private const int ALT_MAX_LENGTH = 255;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $id,
        #[Assert\Length(max: self::ALT_MAX_LENGTH, maxMessage: 'validation.processing_image_alt_too_long')]
        public ?string $alt = null,
    ) {}
}
