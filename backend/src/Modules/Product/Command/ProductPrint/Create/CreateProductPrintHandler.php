<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductPrint\Create;

use App\Modules\Product\Entity\ProductPrint\ProductPrint;
use App\Modules\Product\Entity\ProductPrint\ProductPrintRepository;

final readonly class CreateProductPrintHandler
{
    public function __construct(
        private ProductPrintRepository $repository,
    ) {}

    public function handle(CreateProductPrintCommand $command): void
    {
        $link = ProductPrint::create(
            ProductId: $command->ProductId,
            printId: $command->printId,
        );

        $this->repository->add($link);
    }
}
