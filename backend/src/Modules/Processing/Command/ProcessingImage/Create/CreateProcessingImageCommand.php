<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\ProcessingImage\Create;

use App\Modules\Processing\ReadModel\ProcessingImage\ProcessingImageItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateProcessingImageCommand
{
    /**
     * @param int $currentUserId
     * @param int $currentUserRole
     * @param int $processingId
     * @param list<ProcessingImageItem> $images
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $processingId,
        #[Assert\NotBlank]
        #[Assert\All([
            new Assert\Type(ProcessingImageItem::class),
        ])]
        public array $images,
    ) {}
}
