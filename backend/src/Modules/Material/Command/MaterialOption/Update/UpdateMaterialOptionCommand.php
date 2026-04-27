<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialOption\Update;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateMaterialOptionCommand
{
    private const int NAME_MAX_LENGTH = 255;
    private const int PRICING_TYPE_MIN = 1;
    private const int PRICING_TYPE_MAX = 2;

    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_option_id_required')]
        #[Assert\Positive]
        public int $id,

        #[Assert\NotBlank(message: 'validation.material_option_name_required')]
        #[Assert\Length(
            min: 1,
            max: self::NAME_MAX_LENGTH,
            maxMessage: 'validation.material_option_name_too_long',
        )]
        public string $name,

        #[Assert\NotBlank(message: 'validation.material_option_pricing_type_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.material_option_pricing_type_invalid',
            min: self::PRICING_TYPE_MIN,
            max: self::PRICING_TYPE_MAX
        )]
        public int $pricingType,

        public bool $isCut = false,
    ) {}
}
