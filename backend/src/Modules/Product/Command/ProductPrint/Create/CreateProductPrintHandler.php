<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductPrint\Create;

use App\Components\Cacher\Cacher;
use App\Modules\Product\Entity\ProductPrint\ProductPrint;
use App\Modules\Product\Entity\ProductPrint\ProductPrintRepository;

final readonly class CreateProductPrintHandler
{
    public function __construct(
        private ProductPrintRepository $repository,
        private Cacher $cacher,
    ) {}

    public function handle(CreateProductPrintCommand $command): void
    {
        $link = ProductPrint::create(
            productId: $command->productId,
            printId: $command->printId,
        );

        $this->repository->add($link);
        $this->cacher->deleteTag('product_by_id_' . $command->productId);
    }
}
