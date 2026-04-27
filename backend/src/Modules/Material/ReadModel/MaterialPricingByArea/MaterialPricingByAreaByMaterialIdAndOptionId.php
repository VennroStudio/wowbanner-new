<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingByArea;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\AreaRangeType;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use App\Modules\Material\ReadModel\MaterialPricingByArea\Interface\MaterialPricingByAreaModelInterface;
use Override;

final readonly class MaterialPricingByAreaByMaterialIdAndOptionId implements MaterialPricingByAreaModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $materialId,
        public int $optionId,
        public DpiType $dpiType,
        public AreaRangeType $areaRangeType,
        public string $price,
        public string $cost,
        public string $printHours,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     material_id: int,
     *     option_id: int,
     *     dpi_type: int,
     *     area_range_type: int,
     *     price: string|float|int,
     *     cost: string|float|int,
     *     print_hours: string|float|int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            materialId: (int) $row['material_id'],
            optionId: (int) $row['option_id'],
            dpiType: DpiType::from((int) $row['dpi_type']),
            areaRangeType: AreaRangeType::from((int) $row['area_range_type']),
            price: (string) $row['price'],
            cost: (string) $row['cost'],
            printHours: (string) $row['print_hours'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getMaterialId(): int
    {
        return $this->materialId;
    }

    #[Override]
    public function getOptionId(): int
    {
        return $this->optionId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'material_id'     => $this->materialId,
            'option_id'       => $this->optionId,
            'dpi_type'        => [
                'id' => $this->dpiType->value,
                'label' => $this->dpiType->getLabel()],
            'area_range_type' => [
                'id'    => $this->areaRangeType->value,
                'label' => $this->areaRangeType->getLabel(),
            ],
            'price'           => $this->price,
            'cost'            => $this->cost,
            'print_hours'     => $this->printHours,
        ];
    }
}
