<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderFile\Create\CreateOrderFileCommand;
use App\Modules\Order\Command\OrderFile\Create\CreateOrderFileHandler;
use App\Modules\Order\Command\OrderFile\Update\UpdateOrderFileCommand;
use App\Modules\Order\Command\OrderFile\Update\UpdateOrderFileHandler;
use App\Modules\Order\Entity\OrderFile\OrderFileRepository;
use App\Modules\Order\ReadModel\OrderFile\OrderFileItem;

final readonly class OrderFileSyncerService
{
    public function __construct(
        private OrderFileRepository $repository,
        private CreateOrderFileHandler $createHandler,
        private UpdateOrderFileHandler $updateHandler,
    ) {}

    /**
     * @param list<OrderFileItem> $items
     */
    public function sync(int $orderId, array $items): void
    {
        if ($items === []) {
            return;
        }

        $currentItems = $this->repository->findByOrderId($orderId);
        $currentIds = array_map(static fn ($item) => $item->id, $currentItems);

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateOrderFileCommand(
                    id: $item->id,
                    tmpFilePath: $item->tmpFilePath,
                    originalName: $item->originalName,
                ));
                continue;
            }

            $this->createHandler->handle(new CreateOrderFileCommand(
                orderId: $orderId,
                tmpFilePath: $item->tmpFilePath,
                originalName: $item->originalName,
            ));
        }
    }
}
