<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\Product\Delete;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Product\Command\ProductMaterial\Delete\DeleteProductMaterialCommand;
use App\Modules\Product\Command\ProductMaterial\Delete\DeleteProductMaterialHandler;
use App\Modules\Product\Command\ProductPrint\Delete\DeleteProductPrintCommand;
use App\Modules\Product\Command\ProductPrint\Delete\DeleteProductPrintHandler;
use App\Modules\Product\Entity\Product\ProductRepository;
use App\Modules\Product\Entity\ProductMaterial\ProductMaterialRepository;
use App\Modules\Product\Entity\ProductPrint\ProductPrintRepository;
use App\Modules\Product\Permission\ProductPermission;
use App\Modules\Product\Service\ProductPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteProductHandler
{
    public function __construct(
        private ProductRepository            $repository,
        private ProductMaterialRepository    $productMaterialRepository,
        private ProductPrintRepository       $productPrintRepository,
        private FlusherInterface             $flusher,
        private ProductPermissionService     $permissionService,
        private DeleteProductMaterialHandler $deleteProductMaterialHandler,
        private DeleteProductPrintHandler    $deleteProductPrintHandler,
        private Cacher                       $cacher,
    ) {}

    /**
     * @throws AccessDeniedException
     */
    public function handle(DeleteProductCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProductPermission::DELETE,
        );

        $product = $this->repository->getById($command->id);

        $this->deleteMaterials($command->id);
        $this->deletePrints($command->id);

        $this->repository->remove($product);

        $this->cacher->delete('product_by_id_' . $command->id);

        $this->flusher->flush();
    }

    private function deleteMaterials(int $productId): void
    {
        $materials = $this->productMaterialRepository->findByProductId($productId);
        foreach ($materials as $material) {
            if ($material->id === null) {
                continue;
            }
            $this->deleteProductMaterialHandler->handle(
                new DeleteProductMaterialCommand($material->id),
            );
        }
    }

    private function deletePrints(int $productId): void
    {
        $prints = $this->productPrintRepository->findByProductId($productId);
        foreach ($prints as $print) {
            if ($print->id === null) {
                continue;
            }
            $this->deleteProductPrintHandler->handle(
                new DeleteProductPrintCommand($print->id),
            );
        }
    }
}
