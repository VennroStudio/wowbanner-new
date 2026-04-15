<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionMaterial\Delete;

use App\Modules\Production\Entity\ProductionMaterial\ProductionMaterialRepository;

final readonly class DeleteProductionMaterialHandler
{
    public function __construct(
        private ProductionMaterialRepository $repository,
    ) {}

    public function handle(DeleteProductionMaterialCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $this->repository->remove($link);
    }
}
