<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionPrint\Create;

use App\Modules\Production\Entity\ProductionPrint\ProductionPrint;
use App\Modules\Production\Entity\ProductionPrint\ProductionPrintRepository;

final readonly class CreateProductionPrintHandler
{
    public function __construct(
        private ProductionPrintRepository $repository,
    ) {}

    public function handle(CreateProductionPrintCommand $command): void
    {
        $link = ProductionPrint::create(
            productionId: $command->productionId,
            printId: $command->printId,
        );

        $this->repository->add($link);
    }
}
