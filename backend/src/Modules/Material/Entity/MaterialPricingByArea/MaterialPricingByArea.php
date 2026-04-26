<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByArea;

use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\AreaRangeType;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'material_pricing_by_area')]
#[ORM\Index(name: 'idx_m_pricing_area_material_id', columns: ['material_id'])]
#[ORM\Index(name: 'idx_m_pricing_area_option_id', columns: ['option_id'])]
class MaterialPricingByArea
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $optionId;

    #[ORM\Column(type: Types::INTEGER, enumType: DpiType::class)]
    private(set) DpiType $dpiType;

    #[ORM\Column(type: Types::INTEGER, enumType: AreaRangeType::class)]
    private(set) AreaRangeType $areaRangeType;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $price;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $cost;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $printHours;

    private function __construct(
        int $materialId,
        int $optionId,
        DpiType $dpiType,
        AreaRangeType $areaRangeType,
        string $price,
        string $cost,
        string $printHours,
    ) {
        $this->materialId = $materialId;
        $this->optionId = $optionId;
        $this->dpiType = $dpiType;
        $this->areaRangeType = $areaRangeType;
        $this->price = $price;
        $this->cost = $cost;
        $this->printHours = $printHours;
    }

    public static function create(
        int $materialId,
        int $optionId,
        DpiType $dpiType,
        AreaRangeType $areaRangeType,
        string $price,
        string $cost,
        string $printHours,
    ): self {
        return new self(
            $materialId,
            $optionId,
            $dpiType,
            $areaRangeType,
            $price,
            $cost,
            $printHours
        );
    }

    public function edit(
        DpiType $dpiType,
        AreaRangeType $areaRangeType,
        string $price,
        string $cost,
        string $printHours,
    ): void {
        $this->dpiType = $dpiType;
        $this->areaRangeType = $areaRangeType;
        $this->price = $price;
        $this->cost = $cost;
        $this->printHours = $printHours;
    }
}
