<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionPrint\Delete;

use App\Modules\Production\Entity\ProductionPrint\ProductionPrintRepository;

final readonly class DeleteProductionPrintHandler
{
    public function __construct(
        private ProductionPrintRepository $repository,
    ) {}

    public function handle(DeleteProductionPrintCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $this->repository->remove($link);
    }
}
