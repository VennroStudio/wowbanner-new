<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\ProductMaterial;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_materials')]
#[ORM\Index(name: 'idx_product_id', columns: ['product_id'])]
#[ORM\Index(name: 'idx_material_option_id', columns: ['material_option_id'])]
class ProductMaterial
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $productId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialOptionId;

    private function __construct(int $productId, int $materialOptionId)
    {
        $this->productId = $productId;
        $this->materialOptionId = $materialOptionId;
    }

    public static function create(int $productId, int $materialOptionId): self
    {
        return new self($productId, $materialOptionId);
    }

    public function edit(int $productId, int $materialOptionId): void
    {
        $this->productId = $productId;
        $this->materialOptionId = $materialOptionId;
    }
}
