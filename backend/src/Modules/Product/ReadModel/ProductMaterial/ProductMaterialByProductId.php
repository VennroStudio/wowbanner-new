<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductMaterial;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Product\ReadModel\ProductMaterial\Interface\ProductMaterialModelInterface;
use Override;

final readonly class ProductMaterialByProductId implements ProductMaterialModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $ProductId,
        public int $materialOptionId,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     Product_id: int,
     *     material_option_id: int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            ProductId: $row['Product_id'],
            materialOptionId: $row['material_option_id'],
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
        return $this->ProductId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'Product_id'      => $this->ProductId,
            'material_option_id' => $this->materialOptionId,
        ];
    }
}
