<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Update;

use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use App\Modules\Order\Service\OrderFileStorageService;
use Random\RandomException;

final readonly class UpdateOrderFileHandler
{
    public function __construct(
        private OrderFileRepository $repository,
        private OrderFileStorageService $storageService,
    ) {}

    /**
     * @throws RandomException
     */
    public function handle(UpdateOrderFileCommand $command): void
    {
        $orderFile = $this->repository->getById($command->id);

        $uploaded = $this->storageService->replace(
            orderId: (int) $orderFile->orderId,
            oldDiskPath: $orderFile->diskPath,
            tmpFilePath: $command->tmpFilePath,
            originalName: $command->originalName,
        );

        $orderFile->edit(
            diskPath: $uploaded['diskPath'],
            fileName: $uploaded['fileName'],
            originalName: $uploaded['originalName'],
        );
    }
}
