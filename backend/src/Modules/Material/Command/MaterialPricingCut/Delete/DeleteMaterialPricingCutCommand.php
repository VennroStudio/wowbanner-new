<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingCut\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteMaterialPricingCutCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_pricing_cut_id_required')]
        #[Assert\Positive]
        public int $id,
    ) {}
}
