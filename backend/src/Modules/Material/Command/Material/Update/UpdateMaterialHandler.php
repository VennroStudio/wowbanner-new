<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Update;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionItem;
use App\Modules\Material\Service\MaterialOptionSyncerService;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\Material\Service\MaterialPricingByAreaSyncerService;
use App\Modules\Material\Service\MaterialPricingByPieceSyncerService;
use App\Modules\Material\Service\MaterialPricingCutSyncerService;
use App\Modules\Material\Service\MaterialProcessingLinkSyncerService;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialOptionRepository $materialOptionRepository,
        private MaterialPermissionService $materialPermissionService,
        private MaterialOptionSyncerService $materialOptionSyncer,
        private MaterialPricingByAreaSyncerService $materialPricingByAreaSyncer,
        private MaterialPricingByPieceSyncerService $materialPricingByPieceSyncer,
        private MaterialPricingCutSyncerService $materialPricingCutSyncer,
        private MaterialProcessingLinkSyncerService $materialProcessingLinkSyncer,
        private FlusherInterface $flusher,
        private Cacher $cacher,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(UpdateMaterialCommand $command): void
    {
        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::UPDATE,
        );

        $optionIdsBefore = array_map(
            static fn (object $o): int => (int) $o->id,
            $this->materialOptionRepository->findByMaterialId($command->materialId)
        );

        $material = $this->materialRepository->getById($command->materialId);

        $material->edit(
            name: $command->name,
            description: $command->description,
        );

        $this->materialOptionSyncer->sync($command->materialId, $command->options);

        $this->cacher->delete('material_by_id_' . $command->materialId);

        $this->flusher->flush();

        $resolvedOptionIds = $this->resolveOptionIdsAfterSync(
            $command->materialId,
            $command->options,
            $optionIdsBefore
        );

        foreach ($command->options as $i => $item) {
            $optionId = $resolvedOptionIds[$i];
            $this->materialPricingByAreaSyncer->sync(
                $command->materialId,
                $optionId,
                $item->pricingByArea
            );
            $this->materialPricingByPieceSyncer->sync(
                $command->materialId,
                $optionId,
                $item->pricingByPiece
            );
            $this->materialPricingCutSyncer->sync(
                $command->materialId,
                $optionId,
                $item->pricingByCut
            );
            $this->materialProcessingLinkSyncer->sync(
                $command->materialId,
                $optionId,
                $item->processings
            );
        }

        $this->flusher->flush();

        $this->materialQueryCacheInvalidator->invalidateByMaterialId($command->materialId);
        foreach ($this->materialOptionRepository->findByMaterialId($command->materialId) as $option) {
            $this->materialQueryCacheInvalidator->invalidateMaterialOption(
                (int) $option->id,
                $command->materialId
            );
        }
    }

    /**
     * @param list<MaterialOptionItem> $items
     * @param list<int> $idsBeforeFlush
     *
     * @return list<int>
     */
    private function resolveOptionIdsAfterSync(int $materialId, array $items, array $idsBeforeFlush): array
    {
        $before = array_fill_keys($idsBeforeFlush, true);
        $after = $this->materialOptionRepository->findByMaterialId($materialId);
        $afterIds = array_map(static fn (object $o): int => (int) $o->id, $after);
        $newIds = array_values(
            array_filter(
                $afterIds,
                static fn (int $id): bool => !isset($before[$id])
            )
        );
        sort($newIds);
        $i = 0;
        $resolved = [];
        foreach ($items as $item) {
            if ($item->id !== null) {
                $resolved[] = $item->id;
            } else {
                $resolved[] = $newIds[$i];
                ++$i;
            }
        }

        return $resolved;
    }
}
