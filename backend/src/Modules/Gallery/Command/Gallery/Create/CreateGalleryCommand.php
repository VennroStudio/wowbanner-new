<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Command\Gallery\Create;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateGalleryCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $id,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $type,
        #[Assert\NotBlank]
        public string $tmpFilePath,
        public string $alt = '',
    ) {}
}
