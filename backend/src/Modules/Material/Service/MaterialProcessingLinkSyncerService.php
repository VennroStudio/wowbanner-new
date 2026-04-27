<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Modules\Material\Command\MaterialProcessing\Create\CreateMaterialProcessingCommand;
use App\Modules\Material\Command\MaterialProcessing\Create\CreateMaterialProcessingHandler;
use App\Modules\Material\Command\MaterialProcessing\Delete\DeleteMaterialProcessingCommand;
use App\Modules\Material\Command\MaterialProcessing\Delete\DeleteMaterialProcessingHandler;
use App\Modules\Material\Command\MaterialProcessing\Update\UpdateMaterialProcessingCommand;
use App\Modules\Material\Command\MaterialProcessing\Update\UpdateMaterialProcessingHandler;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;
use App\Modules\Material\ReadModel\MaterialProcessing\MaterialProcessingLinkItem;

final readonly class MaterialProcessingLinkSyncerService
{
    public function __construct(
        private MaterialProcessingRepository $repository,
        private CreateMaterialProcessingHandler $createHandler,
        private UpdateMaterialProcessingHandler $updateHandler,
        private DeleteMaterialProcessingHandler $deleteHandler,
    ) {}

    /**
     * @param list<MaterialProcessingLinkItem> $items
     */
    public function sync(int $materialId, int $optionId, array $items): void
    {
        $all = $this->repository->findByMaterialId($materialId);
        $forOption = array_values(
            array_filter(
                $all,
                static fn (object $e): bool => (int) $e->optionId === $optionId
            )
        );
        $currentIds = array_map(static fn (object $e): int => (int) $e->id, $forOption);
        $wantedIds = array_values(
            array_filter(
                array_map(
                    static fn (MaterialProcessingLinkItem $i) => $i->id,
                    $items
                ),
                static fn (?int $id) => $id !== null
            )
        );

        foreach ($forOption as $row) {
            if (!\in_array((int) $row->id, $wantedIds, true)) {
                $this->deleteHandler->handle(
                    new DeleteMaterialProcessingCommand(id: (int) $row->id)
                );
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(
                    new UpdateMaterialProcessingCommand(
                        id: $item->id,
                        materialId: $materialId,
                        optionId: $optionId,
                        processingId: $item->processingId,
                    )
                );
            } else {
                $this->createHandler->handle(
                    new CreateMaterialProcessingCommand(
                        materialId: $materialId,
                        optionId: $optionId,
                        processingId: $item->processingId,
                    )
                );
            }
        }
    }
}
