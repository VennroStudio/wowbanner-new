<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByArea\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteMaterialPricingByAreaCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_pricing_by_area_id_required')]
        #[Assert\Positive]
        public int $id,
    ) {}
}
