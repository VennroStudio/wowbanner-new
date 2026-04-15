<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\Production\Create;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Production\Command\ProductionMaterial\Create\CreateProductionMaterialCommand;
use App\Modules\Production\Command\ProductionMaterial\Create\CreateProductionMaterialHandler;
use App\Modules\Production\Command\ProductionPrint\Create\CreateProductionPrintCommand;
use App\Modules\Production\Command\ProductionPrint\Create\CreateProductionPrintHandler;
use App\Modules\Production\Entity\Production\Production;
use App\Modules\Production\Entity\Production\ProductionRepository;
use App\Modules\Production\Permission\ProductionPermission;
use App\Modules\Production\ReadModel\ProductionMaterial\ProductionMaterialItem;
use App\Modules\Production\ReadModel\ProductionPrint\ProductionPrintItem;
use App\Modules\Production\Service\ProductionPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class CreateProductionHandler
{
    public function __construct(
        private ProductionRepository $repository,
        private FlusherInterface $flusher,
        private ProductionPermissionService $permissionService,
        private CreateProductionMaterialHandler $createMaterialHandler,
        private CreateProductionPrintHandler $createPrintHandler,
    ) {}

    public function handle(CreateProductionCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProductionPermission::CREATE,
        );

        $production = Production::create(name: $command->name);

        $this->repository->add($production);

        $this->flusher->flush();

        $this->processMaterials($production, $command->materials);
        $this->processPrints($production, $command->prints);

        $this->flusher->flush();
    }

    /**
     * @param list<ProductionMaterialItem> $items
     */
    private function processMaterials(Production $production, array $items): void
    {
        foreach ($items as $item) {
            $this->createMaterialHandler->handle(new CreateProductionMaterialCommand(
                productionId: (int)$production->id,
                materialOptionId: $item->materialOptionId,
            ));
        }
    }

    /**
     * @param list<ProductionPrintItem> $items
     */
    private function processPrints(Production $production, array $items): void
    {
        foreach ($items as $item) {
            $this->createPrintHandler->handle(new CreateProductionPrintCommand(
                productionId: (int)$production->id,
                printId: $item->printId,
            ));
        }
    }
}
