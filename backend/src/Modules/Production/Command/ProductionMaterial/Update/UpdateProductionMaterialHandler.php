<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionMaterial\Update;

use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterialRepository;

final readonly class UpdateProductionMaterialHandler
{
    public function __construct(
        private ProductionMaterialRepository $repository,
    ) {}

    public function handle(UpdateProductionMaterialCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $link->edit(
            productionId: $command->productionId,
            materialOptionId: $command->materialOptionId,
        );
    }
}
