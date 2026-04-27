<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Modules\Material\Command\MaterialPricingCut\Create\CreateMaterialPricingCutCommand;
use App\Modules\Material\Command\MaterialPricingCut\Create\CreateMaterialPricingCutHandler;
use App\Modules\Material\Command\MaterialPricingCut\Delete\DeleteMaterialPricingCutCommand;
use App\Modules\Material\Command\MaterialPricingCut\Delete\DeleteMaterialPricingCutHandler;
use App\Modules\Material\Command\MaterialPricingCut\Update\UpdateMaterialPricingCutCommand;
use App\Modules\Material\Command\MaterialPricingCut\Update\UpdateMaterialPricingCutHandler;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;
use App\Modules\Material\ReadModel\MaterialPricingCut\MaterialPricingCutItem;

final readonly class MaterialPricingCutSyncerService
{
    public function __construct(
        private MaterialPricingCutRepository $repository,
        private CreateMaterialPricingCutHandler $createHandler,
        private UpdateMaterialPricingCutHandler $updateHandler,
        private DeleteMaterialPricingCutHandler $deleteHandler,
    ) {}

    /**
     * @param list<MaterialPricingCutItem> $items
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
                    static fn (MaterialPricingCutItem $i) => $i->id,
                    $items
                ),
                static fn (?int $id) => $id !== null
            )
        );

        foreach ($forOption as $row) {
            if (!\in_array((int) $row->id, $wantedIds, true)) {
                $this->deleteHandler->handle(
                    new DeleteMaterialPricingCutCommand(id: (int) $row->id)
                );
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(
                    new UpdateMaterialPricingCutCommand(
                        id: $item->id,
                        type: $item->type,
                        price: $item->price,
                    )
                );
            } else {
                $this->createHandler->handle(
                    new CreateMaterialPricingCutCommand(
                        materialId: $materialId,
                        optionId: $optionId,
                        type: $item->type,
                        price: $item->price,
                    )
                );
            }
        }
    }
}
