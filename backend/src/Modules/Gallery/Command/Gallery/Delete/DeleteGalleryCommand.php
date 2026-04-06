<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Command\Gallery\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteGalleryCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $galleryId,
    ) {}
}
