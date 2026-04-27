<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Modules\Material\Command\MaterialPricingByArea\Create\CreateMaterialPricingByAreaCommand;
use App\Modules\Material\Command\MaterialPricingByArea\Create\CreateMaterialPricingByAreaHandler;
use App\Modules\Material\Command\MaterialPricingByArea\Delete\DeleteMaterialPricingByAreaCommand;
use App\Modules\Material\Command\MaterialPricingByArea\Delete\DeleteMaterialPricingByAreaHandler;
use App\Modules\Material\Command\MaterialPricingByArea\Update\UpdateMaterialPricingByAreaCommand;
use App\Modules\Material\Command\MaterialPricingByArea\Update\UpdateMaterialPricingByAreaHandler;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;
use App\Modules\Material\ReadModel\MaterialPricingByArea\MaterialPricingByAreaItem;

final readonly class MaterialPricingByAreaSyncerService
{
    public function __construct(
        private MaterialPricingByAreaRepository $repository,
        private CreateMaterialPricingByAreaHandler $createHandler,
        private UpdateMaterialPricingByAreaHandler $updateHandler,
        private DeleteMaterialPricingByAreaHandler $deleteHandler,
    ) {}

    /**
     * @param list<MaterialPricingByAreaItem> $items
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
                    static fn (MaterialPricingByAreaItem $i) => $i->id,
                    $items
                ),
                static fn (?int $id) => $id !== null
            )
        );

        foreach ($forOption as $row) {
            if (!\in_array((int) $row->id, $wantedIds, true)) {
                $this->deleteHandler->handle(
                    new DeleteMaterialPricingByAreaCommand(id: (int) $row->id)
                );
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(
                    new UpdateMaterialPricingByAreaCommand(
                        id: $item->id,
                        dpiType: $item->dpiType,
                        areaRangeType: $item->areaRangeType,
                        price: $item->price,
                        cost: $item->cost,
                        printHours: $item->printHours,
                    )
                );
            } else {
                $this->createHandler->handle(
                    new CreateMaterialPricingByAreaCommand(
                        materialId: $materialId,
                        optionId: $optionId,
                        dpiType: $item->dpiType,
                        areaRangeType: $item->areaRangeType,
                        price: $item->price,
                        cost: $item->cost,
                        printHours: $item->printHours,
                    )
                );
            }
        }
    }
}
