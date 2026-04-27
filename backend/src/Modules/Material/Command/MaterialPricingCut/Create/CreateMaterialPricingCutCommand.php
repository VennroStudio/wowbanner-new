<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingCut\Create;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateMaterialPricingCutCommand
{
    private const string MONEY_PATTERN = '/^\d+(\.\d{1,2})?$/';
    private const int TYPE_MIN = 1;
    private const int TYPE_MAX = 2;

    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_id_required')]
        #[Assert\Positive]
        public int $materialId,

        #[Assert\NotBlank(message: 'validation.material_option_id_required')]
        #[Assert\Positive]
        public int $optionId,

        #[Assert\NotBlank(message: 'validation.material_pricing_cut_type_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.material_pricing_cut_type_invalid',
            min: self::TYPE_MIN,
            max: self::TYPE_MAX
        )]
        public int $type,

        #[Assert\NotBlank(message: 'validation.material_price_required')]
        #[Assert\Regex(pattern: self::MONEY_PATTERN, message: 'validation.material_price_invalid')]
        public string $price,
    ) {}
}
