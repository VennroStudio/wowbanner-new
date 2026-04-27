<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByPiece\Create;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateMaterialPricingByPieceCommand
{
    private const string MONEY_PATTERN = '/^\d+(\.\d{1,2})?$/';
    private const int VARIANT_MIN = 1;
    private const int VARIANT_MAX = 3;

    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_id_required')]
        #[Assert\Positive]
        public int $materialId,

        #[Assert\NotBlank(message: 'validation.material_option_id_required')]
        #[Assert\Positive]
        public int $optionId,

        #[Assert\NotBlank(message: 'validation.material_pricing_by_piece_variant_type_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.material_pricing_by_piece_variant_type_invalid',
            min: self::VARIANT_MIN,
            max: self::VARIANT_MAX
        )]
        public int $variantType,

        #[Assert\NotBlank(message: 'validation.material_price_required')]
        #[Assert\Regex(pattern: self::MONEY_PATTERN, message: 'validation.material_price_invalid')]
        public string $price,

        #[Assert\NotBlank(message: 'validation.material_cost_required')]
        #[Assert\Regex(pattern: self::MONEY_PATTERN, message: 'validation.material_cost_invalid')]
        public string $cost,

        #[Assert\NotBlank(message: 'validation.material_print_hours_required')]
        #[Assert\Regex(pattern: self::MONEY_PATTERN, message: 'validation.material_print_hours_invalid')]
        public string $printHours,
    ) {}
}
