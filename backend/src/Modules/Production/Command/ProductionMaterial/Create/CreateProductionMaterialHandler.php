<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionMaterial\Create;

use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterial;
use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterialRepository;

final readonly class CreateProductionMaterialHandler
{
    public function __construct(
        private ProductionMaterialRepository $repository,
    ) {}

    public function handle(CreateProductionMaterialCommand $command): void
    {
        $link = ProductionMaterial::create(
            productionId: $command->productionId,
            materialOptionId: $command->materialOptionId,
        );

        $this->repository->add($link);
    }
}
