<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByPiece\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteMaterialPricingByPieceCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'validation.material_pricing_by_piece_id_required')]
        #[Assert\Positive]
        public int $id,
    ) {}
}
