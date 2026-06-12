<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Update;

use App\Components\YandexDisk\YandexDiskClient;
use App\Modules\Order\Entity\OrderFile\Fields\Enums\OrderFileDirectory;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use App\Modules\Order\Service\OrderFileNameGeneratorService;
use Random\RandomException;

final readonly class UpdateOrderFileHandler
{
    public function __construct(
        private OrderFileRepository $repository,
        private YandexDiskClient $yandexDiskClient,
        private OrderFileNameGeneratorService $fileNameGenerator,
    ) {}

    /**
     * @throws RandomException
     */
    public function handle(UpdateOrderFileCommand $command): void
    {
        $orderFile = $this->repository->getById($command->id);

        $this->yandexDiskClient->delete($orderFile->diskPath);

        $fileName = $this->fileNameGenerator->generate($command->originalName);
        $diskPath = $this->yandexDiskClient->upload(
            tmpFilePath: $command->tmpFilePath,
            folder: OrderFileDirectory::FILES->getPath((int) $orderFile->orderId),
            fileName: $fileName,
        );

        $orderFile->edit(
            diskPath: $diskPath,
            fileName: $fileName,
            originalName: $command->originalName,
        );
    }
}
