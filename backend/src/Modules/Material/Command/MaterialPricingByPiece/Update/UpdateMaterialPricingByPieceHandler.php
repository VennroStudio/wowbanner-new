<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByPiece\Update;

use App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums\VariantType;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class UpdateMaterialPricingByPieceHandler
{
    public function __construct(
        private MaterialPricingByPieceRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(UpdateMaterialPricingByPieceCommand $command): void
    {
        $entity = $this->repository->getById($command->id);

        $entity->edit(
            variantType: VariantType::from($command->variantType),
            price: $command->price,
            cost: $command->cost,
            printHours: $command->printHours,
        );

        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
            $entity->materialId,
            $entity->optionId
        );
    }
}
