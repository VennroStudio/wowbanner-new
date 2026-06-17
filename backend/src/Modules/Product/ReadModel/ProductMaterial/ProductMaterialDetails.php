<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductMaterial;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Product\ReadModel\ProductMaterial\Interface\ProductMaterialModelInterface;
use Override;

final readonly class ProductMaterialDetails implements ProductMaterialModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $productId,
        public int $materialId,
        public int $materialOptionId,
        public string $materialName,
        public string $materialOptionName,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'                   => 'id',
            'product_id'           => 'product_id',
            'material_id'          => 'material_id',
            'material_option_id'   => 'material_option_id',
            'material_name'        => 'm.name',
            'material_option_name' => 'mo.name',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     product_id: int,
     *     material_id: int,
     *     material_option_id: int,
     *     material_name: string,
     *     material_option_name: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            productId: (int)$row['product_id'],
            materialId: (int)$row['material_id'],
            materialOptionId: (int)$row['material_option_id'],
            materialName: $row['material_name'],
            materialOptionName: $row['material_option_name'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getProductId(): int
    {
        return $this->productId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'                   => $this->id,
            'material_id'          => $this->materialId,
            'material_option_id'   => $this->materialOptionId,
            'material_name'        => $this->materialName,
            'material_option_name' => $this->materialOptionName,
        ];
    }
}
