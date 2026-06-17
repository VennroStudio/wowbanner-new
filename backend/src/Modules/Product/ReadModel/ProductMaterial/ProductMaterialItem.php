<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductMaterial;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductMaterialItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.product_material_id_required')]
        #[Assert\GreaterThan(value: 0, message: 'validation.product_material_id_invalid')]
        public int $materialId,
        #[Assert\NotBlank(message: 'validation.product_material_option_id_required')]
        #[Assert\GreaterThan(value: 0, message: 'validation.product_material_option_id_invalid')]
        public int $materialOptionId,
    ) {}
}
