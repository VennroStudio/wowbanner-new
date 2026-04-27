<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Modules\Material\Command\MaterialPricingByPiece\Create\CreateMaterialPricingByPieceCommand;
use App\Modules\Material\Command\MaterialPricingByPiece\Create\CreateMaterialPricingByPieceHandler;
use App\Modules\Material\Command\MaterialPricingByPiece\Delete\DeleteMaterialPricingByPieceCommand;
use App\Modules\Material\Command\MaterialPricingByPiece\Delete\DeleteMaterialPricingByPieceHandler;
use App\Modules\Material\Command\MaterialPricingByPiece\Update\UpdateMaterialPricingByPieceCommand;
use App\Modules\Material\Command\MaterialPricingByPiece\Update\UpdateMaterialPricingByPieceHandler;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;
use App\Modules\Material\ReadModel\MaterialPricingByPiece\MaterialPricingByPieceItem;

final readonly class MaterialPricingByPieceSyncerService
{
    public function __construct(
        private MaterialPricingByPieceRepository $repository,
        private CreateMaterialPricingByPieceHandler $createHandler,
        private UpdateMaterialPricingByPieceHandler $updateHandler,
        private DeleteMaterialPricingByPieceHandler $deleteHandler,
    ) {}

    /**
     * @param list<MaterialPricingByPieceItem> $items
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
                    static fn (MaterialPricingByPieceItem $i) => $i->id,
                    $items
                ),
                static fn (?int $id) => $id !== null
            )
        );

        foreach ($forOption as $row) {
            if (!\in_array((int) $row->id, $wantedIds, true)) {
                $this->deleteHandler->handle(
                    new DeleteMaterialPricingByPieceCommand(id: (int) $row->id)
                );
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(
                    new UpdateMaterialPricingByPieceCommand(
                        id: $item->id,
                        variantType: $item->variantType,
                        price: $item->price,
                        cost: $item->cost,
                        printHours: $item->printHours,
                    )
                );
            } else {
                $this->createHandler->handle(
                    new CreateMaterialPricingByPieceCommand(
                        materialId: $materialId,
                        optionId: $optionId,
                        variantType: $item->variantType,
                        price: $item->price,
                        cost: $item->cost,
                        printHours: $item->printHours,
                    )
                );
            }
        }
    }
}
