<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingByPiece;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums\VariantType;
use App\Modules\Material\ReadModel\MaterialPricingByPiece\Interface\MaterialPricingByPieceModelInterface;
use Override;

final readonly class MaterialPricingByPieceByMaterialIdAndOptionId implements MaterialPricingByPieceModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $materialId,
        public int $optionId,
        public VariantType $variantType,
        public string $price,
        public string $cost,
        public string $printHours,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'           => 'id',
            'material_id'  => 'material_id',
            'option_id'    => 'option_id',
            'variant_type' => 'variant_type',
            'price'        => 'price',
            'cost'         => 'cost',
            'print_hours'  => 'print_hours',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     material_id: int,
     *     option_id: int,
     *     variant_type: int,
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
            variantType: VariantType::from((int) $row['variant_type']),
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
            'id'           => $this->id,
            'variant_type' => [
                'id'    => $this->variantType->value,
                'label' => $this->variantType->getLabel(),
            ],
            'price'        => $this->price,
            'cost'         => $this->cost,
            'print_hours'  => $this->printHours,
        ];
    }
}
