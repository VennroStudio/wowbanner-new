<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductPrint\Delete;

use App\Modules\Product\Entity\ProductPrint\ProductPrintRepository;

final readonly class DeleteProductPrintHandler
{
    public function __construct(
        private ProductPrintRepository $repository,
    ) {}

    public function handle(DeleteProductPrintCommand $command): void
    {
        $link = $this->repository->getById($command->id);

        $this->repository->remove($link);
    }
}
