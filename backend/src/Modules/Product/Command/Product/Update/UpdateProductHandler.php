<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\Product\Update;

use App\Components\Cacher\Cacher;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Product\Entity\Product\ProductRepository;
use App\Modules\Product\Permission\ProductPermission;
use App\Modules\Product\Service\ProductMaterialSyncerService;
use App\Modules\Product\Service\ProductPermissionService;
use App\Modules\Product\Service\ProductPrintSyncerService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateProductHandler
{
    public function __construct(
        private ProductRepository            $repository,
        private FlusherInterface             $flusher,
        private ProductPermissionService     $permissionService,
        private ProductMaterialSyncerService $materialSyncer,
        private ProductPrintSyncerService    $printSyncer,
        private Cacher                       $cacher,
    ) {}

    public function handle(UpdateProductCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProductPermission::UPDATE,
        );

        $product = $this->repository->getById($command->id);

        $product->edit(name: $command->name);

        $this->materialSyncer->sync($command->id, $command->materials);
        $this->printSyncer->sync($command->id, $command->prints);

        $this->cacher->delete('product_by_id_' . $command->id);

        $this->flusher->flush();
    }
}
