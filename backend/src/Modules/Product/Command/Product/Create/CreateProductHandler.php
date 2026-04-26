<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\Product\Create;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Product\Command\ProductMaterial\Create\CreateProductMaterialCommand;
use App\Modules\Product\Command\ProductMaterial\Create\CreateProductMaterialHandler;
use App\Modules\Product\Command\ProductPrint\Create\CreateProductPrintCommand;
use App\Modules\Product\Command\ProductPrint\Create\CreateProductPrintHandler;
use App\Modules\Product\Entity\Product\Product;
use App\Modules\Product\Entity\Product\ProductRepository;
use App\Modules\Product\Permission\ProductPermission;
use App\Modules\Product\ReadModel\ProductMaterial\ProductMaterialItem;
use App\Modules\Product\ReadModel\ProductPrint\ProductPrintItem;
use App\Modules\Product\Service\ProductPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class CreateProductHandler
{
    public function __construct(
        private ProductRepository            $repository,
        private FlusherInterface             $flusher,
        private ProductPermissionService     $permissionService,
        private CreateProductMaterialHandler $createMaterialHandler,
        private CreateProductPrintHandler    $createPrintHandler,
    ) {}

    public function handle(CreateProductCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProductPermission::CREATE,
        );

        $product = Product::create(name: $command->name);

        $this->repository->add($product);

        $this->flusher->flush();

        $this->processMaterials($product, $command->materials);
        $this->processPrints($product, $command->prints);

        $this->flusher->flush();
    }

    /**
     * @param list<ProductMaterialItem> $items
     */
    private function processMaterials(Product $product, array $items): void
    {
        foreach ($items as $item) {
            $this->createMaterialHandler->handle(new CreateProductMaterialCommand(
                productId: (int)$product->id,
                materialOptionId: $item->materialOptionId,
            ));
        }
    }

    /**
     * @param list<ProductPrintItem> $items
     */
    private function processPrints(Product $product, array $items): void
    {
        foreach ($items as $item) {
            $this->createPrintHandler->handle(new CreateProductPrintCommand(
                productId: (int)$product->id,
                printId: $item->printId,
            ));
        }
    }
}
