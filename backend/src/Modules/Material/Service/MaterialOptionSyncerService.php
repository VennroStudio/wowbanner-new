<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Modules\Material\Command\MaterialOption\Create\CreateMaterialOptionCommand;
use App\Modules\Material\Command\MaterialOption\Create\CreateMaterialOptionHandler;
use App\Modules\Material\Command\MaterialOption\Delete\DeleteMaterialOptionCommand;
use App\Modules\Material\Command\MaterialOption\Delete\DeleteMaterialOptionHandler;
use App\Modules\Material\Command\MaterialOption\Update\UpdateMaterialOptionCommand;
use App\Modules\Material\Command\MaterialOption\Update\UpdateMaterialOptionHandler;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionItem;

final readonly class MaterialOptionSyncerService
{
    public function __construct(
        private MaterialOptionRepository $optionRepository,
        private MaterialOptionAttachmentRemover $materialOptionAttachmentRemover,
        private CreateMaterialOptionHandler $createHandler,
        private UpdateMaterialOptionHandler $updateHandler,
        private DeleteMaterialOptionHandler $deleteHandler,
    ) {}

    /**
     * @param list<MaterialOptionItem> $items
     */
    public function sync(int $materialId, array $items): void
    {
        $current = $this->optionRepository->findByMaterialId($materialId);
        $currentIds = array_map(static fn ($o) => (int) $o->id, $current);
        $wantedIds = array_values(
            array_filter(
                array_map(static fn (MaterialOptionItem $i) => $i->id, $items),
                static fn (?int $id) => $id !== null
            )
        );

        foreach ($current as $option) {
            if (!\in_array((int) $option->id, $wantedIds, true)) {
                $oid = (int) $option->id;
                $this->materialOptionAttachmentRemover->removeAllForOption($materialId, $oid);
                $this->deleteHandler->handle(new DeleteMaterialOptionCommand(
                    id: $oid,
                ));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateMaterialOptionCommand(
                    id: $item->id,
                    name: $item->name,
                    pricingType: $item->pricingType,
                    isCut: $item->isCut,
                ));
            } else {
                $this->createHandler->handle(new CreateMaterialOptionCommand(
                    name: $item->name,
                    materialId: $materialId,
                    pricingType: $item->pricingType,
                    isCut: $item->isCut,
                ));
            }
        }
    }
}
