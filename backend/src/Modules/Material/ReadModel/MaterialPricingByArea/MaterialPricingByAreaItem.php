<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingByArea;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class MaterialPricingByAreaItem
{
    private const string MONEY_PATTERN = '/^\d+(\.\d{1,2})?$/';
    private const int DPI_MIN = 1;
    private const int DPI_MAX = 5;
    private const int AREA_MIN = 1;
    private const int AREA_MAX = 5;

    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.material_pricing_by_area_dpi_type_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.material_pricing_by_area_dpi_type_invalid',
            min: self::DPI_MIN,
            max: self::DPI_MAX
        )]
        public int $dpiType,
        #[Assert\NotBlank(message: 'validation.material_pricing_by_area_range_type_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.material_pricing_by_area_range_type_invalid',
            min: self::AREA_MIN,
            max: self::AREA_MAX
        )]
        public int $areaRangeType,
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
