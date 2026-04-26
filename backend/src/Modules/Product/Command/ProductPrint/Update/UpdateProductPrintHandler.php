<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductPrint\Update;

use App\Modules\Product\Entity\ProductPrint\ProductPrintRepository;

final readonly class UpdateProductPrintHandler
{
    public function __construct(
        private ProductPrintRepository $repository,
    ) {}

    public function handle(UpdateProductPrintCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $link->edit(
            productId: $command->productId,
            printId: $command->printId,
        );
    }
}
