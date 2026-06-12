<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Delete;

use App\Components\YandexDisk\YandexDiskClient;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;

final readonly class DeleteOrderFileHandler
{
    public function __construct(
        private OrderFileRepository $repository,
        private YandexDiskClient $yandexDiskClient,
    ) {}

    public function handle(DeleteOrderFileCommand $command): void
    {
        $orderFile = $this->repository->getById($command->id);

        $this->yandexDiskClient->delete($orderFile->diskPath);
        $this->repository->remove($orderFile);
    }
}
