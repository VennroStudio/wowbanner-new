<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\Processing\Update;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProcessingCommand
{
    private const int NAME_MIN_LENGTH = 2;
    private const int NAME_MAX_LENGTH = 255;
    private const int DESCRIPTION_MAX_LENGTH = 65535;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $id,
        #[Assert\NotBlank(message: 'validation.processing_name_required')]
        #[Assert\Length(
            min: self::NAME_MIN_LENGTH,
            max: self::NAME_MAX_LENGTH,
            minMessage: 'validation.processing_name_too_short',
            maxMessage: 'validation.processing_name_too_long',
        )]
        public string $name,
        #[Assert\NotBlank(message: 'validation.processing_type_required')]
        public int $type,
        #[Assert\Length(max: self::DESCRIPTION_MAX_LENGTH, maxMessage: 'validation.processing_description_too_long')]
        public string $description = '',
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/', message: 'validation.processing_cost_price_invalid')]
        public string $costPrice = '0.00',
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/', message: 'validation.processing_price_invalid')]
        public string $price = '0.00',
    ) {}
}
