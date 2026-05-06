<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionItem;

final readonly class MaterialStructureSyncerService
{
    public function __construct(
        private MaterialValidatorService $materialValidatorService,
        private MaterialOptionSyncerService $materialOptionSyncerService,
        private MaterialPricingByAreaSyncerService $materialPricingByAreaSyncerService,
        private MaterialPricingByPieceSyncerService $materialPricingByPieceSyncerService,
        private MaterialPricingCutSyncerService $materialPricingCutSyncerService,
        private MaterialProcessingLinkSyncerService $materialProcessingLinkSyncerService,
        private FlusherInterface $flusher,
    ) {}

    /**
     * @param list<MaterialOptionItem> $options
     */
    public function sync(int $materialId, array $options): void
    {
        $this->materialValidatorService->validateOptions($options);

        $optionIds = $this->materialOptionSyncerService->sync($materialId, $options);

        foreach ($options as $index => $option) {
            $optionId = $optionIds[$index];

            $this->materialPricingByAreaSyncerService->sync(
                materialId: $materialId,
                optionId: $optionId,
                items: $option->pricingByArea,
            );
            $this->materialPricingByPieceSyncerService->sync(
                materialId: $materialId,
                optionId: $optionId,
                items: $option->pricingByPiece,
            );
            $this->materialPricingCutSyncerService->sync(
                materialId: $materialId,
                optionId: $optionId,
                items: $option->pricingByCut,
            );
            $this->materialProcessingLinkSyncerService->sync(
                materialId: $materialId,
                optionId: $optionId,
                items: $option->processings,
            );
        }

        $this->flusher->flush();
    }
}
