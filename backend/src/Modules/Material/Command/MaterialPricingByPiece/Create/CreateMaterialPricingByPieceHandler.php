<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByPiece\Create;

use App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums\VariantType;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPiece;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class CreateMaterialPricingByPieceHandler
{
    public function __construct(
        private MaterialPricingByPieceRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(CreateMaterialPricingByPieceCommand $command): void
    {
        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext(
            $command->materialId,
            $command->optionId
        );

        $entity = MaterialPricingByPiece::create(
            materialId: $command->materialId,
            optionId: $command->optionId,
            variantType: VariantType::from($command->variantType),
            price: $command->price,
            cost: $command->cost,
            printHours: $command->printHours,
        );

        $this->repository->add($entity);
    }
}
