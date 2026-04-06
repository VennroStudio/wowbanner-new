<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Command\Gallery\Update;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateGalleryCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $galleryId,
        public string $alt = '',
    ) {}
}
