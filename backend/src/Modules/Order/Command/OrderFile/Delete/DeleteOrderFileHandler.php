<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Delete;

use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use App\Modules\Order\Service\OrderFileStorageService;

final readonly class DeleteOrderFileHandler
{
    public function __construct(
        private OrderFileRepository $repository,
        private OrderFileStorageService $storageService,
    ) {}

    public function handle(DeleteOrderFileCommand $command): void
    {
        $orderFile = $this->repository->getById($command->id);

        $this->storageService->delete($orderFile->diskPath);
        $this->repository->remove($orderFile);
    }
}
