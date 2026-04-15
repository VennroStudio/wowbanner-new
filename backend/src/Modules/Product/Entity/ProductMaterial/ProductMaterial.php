<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity\ProductMaterial;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'Product_materials')]
#[ORM\Index(name: 'idx_Product_id', columns: ['Product_id'])]
#[ORM\Index(name: 'idx_material_option_id', columns: ['material_option_id'])]
class ProductMaterial
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $ProductId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialOptionId;

    private function __construct(int $ProductId, int $materialOptionId)
    {
        $this->ProductId = $ProductId;
        $this->materialOptionId = $materialOptionId;
    }

    public static function create(int $ProductId, int $materialOptionId): self
    {
        return new self($ProductId, $materialOptionId);
    }

    public function edit(int $ProductId, int $materialOptionId): void
    {
        $this->ProductId = $ProductId;
        $this->materialOptionId = $materialOptionId;
    }
}
