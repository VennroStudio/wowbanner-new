<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionPrint\Update;

use App\Modules\Production\Entity\ProductionPrint\ProductionPrintRepository;

final readonly class UpdateProductionPrintHandler
{
    public function __construct(
        private ProductionPrintRepository $repository,
    ) {}

    public function handle(UpdateProductionPrintCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $link->edit(
            productionId: $command->productionId,
            printId: $command->printId,
        );
    }
}
