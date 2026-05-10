<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Create;

use App\Modules\Order\Entity\OrderFile\OrderFile;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use App\Modules\Order\Service\OrderFileStorageService;
use Random\RandomException;

final readonly class CreateOrderFileHandler
{
    public function __construct(
        private OrderFileRepository $repository,
        private OrderFileStorageService $storageService,
    ) {}

    /**
     * @throws RandomException
     */
    public function handle(CreateOrderFileCommand $command): void
    {
        $uploaded = $this->storageService->upload(
            orderId: $command->orderId,
            tmpFilePath: $command->tmpFilePath,
            originalName: $command->originalName,
        );

        $orderFile = OrderFile::create(
            orderId: $command->orderId,
            diskPath: $uploaded['diskPath'],
            fileName: $uploaded['fileName'],
            originalName: $uploaded['originalName'],
        );

        $this->repository->add($orderFile);
    }
}
