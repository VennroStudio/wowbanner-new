<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Create;

use App\Components\YandexDisk\YandexDiskClient;
use App\Modules\Order\Entity\OrderFile\Fields\Enums\OrderFileDirectory;
use App\Modules\Order\Entity\OrderFile\OrderFile;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use App\Modules\Order\Service\OrderFileNameGeneratorService;
use Random\RandomException;

final readonly class CreateOrderFileHandler
{
    public function __construct(
        private OrderFileRepository $repository,
        private YandexDiskClient $yandexDiskClient,
        private OrderFileNameGeneratorService $fileNameGenerator,
    ) {}

    /**
     * @throws RandomException
     */
    public function handle(CreateOrderFileCommand $command): void
    {
        $fileName = $this->fileNameGenerator->generate($command->originalName);
        $diskPath = $this->yandexDiskClient->upload(
            tmpFilePath: $command->tmpFilePath,
            folder: OrderFileDirectory::FILES->getPath($command->orderId),
            fileName: $fileName,
        );

        $orderFile = OrderFile::create(
            orderId: $command->orderId,
            diskPath: $diskPath,
            fileName: $fileName,
            originalName: $command->originalName,
        );

        $this->repository->add($orderFile);
    }
}
