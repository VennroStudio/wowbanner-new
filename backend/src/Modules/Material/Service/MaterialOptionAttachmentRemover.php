<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Modules\Material\Command\MaterialPricingByArea\Delete\DeleteMaterialPricingByAreaCommand;
use App\Modules\Material\Command\MaterialPricingByArea\Delete\DeleteMaterialPricingByAreaHandler;
use App\Modules\Material\Command\MaterialPricingByPiece\Delete\DeleteMaterialPricingByPieceCommand;
use App\Modules\Material\Command\MaterialPricingByPiece\Delete\DeleteMaterialPricingByPieceHandler;
use App\Modules\Material\Command\MaterialPricingCut\Delete\DeleteMaterialPricingCutCommand;
use App\Modules\Material\Command\MaterialPricingCut\Delete\DeleteMaterialPricingCutHandler;
use App\Modules\Material\Command\MaterialProcessing\Delete\DeleteMaterialProcessingCommand;
use App\Modules\Material\Command\MaterialProcessing\Delete\DeleteMaterialProcessingHandler;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;

final readonly class MaterialOptionAttachmentRemover
{
    public function __construct(
        private MaterialPricingByAreaRepository $materialPricingByAreaRepository,
        private MaterialPricingByPieceRepository $materialPricingByPieceRepository,
        private MaterialPricingCutRepository $materialPricingCutRepository,
        private MaterialProcessingRepository $materialProcessingRepository,
        private DeleteMaterialPricingByAreaHandler $deleteMaterialPricingByAreaHandler,
        private DeleteMaterialPricingByPieceHandler $deleteMaterialPricingByPieceHandler,
        private DeleteMaterialPricingCutHandler $deleteMaterialPricingCutHandler,
        private DeleteMaterialProcessingHandler $deleteMaterialProcessingHandler,
    ) {}

    public function removeAllForOption(int $materialId, int $optionId): void
    {
        $matchOption = static fn (object $row): bool => (int) $row->optionId === $optionId;

        foreach ($this->materialPricingByAreaRepository->findByMaterialId($materialId) as $row) {
            if ($matchOption($row)) {
                $this->deleteMaterialPricingByAreaHandler->handle(
                    new DeleteMaterialPricingByAreaCommand(id: (int) $row->id)
                );
            }
        }
        foreach ($this->materialPricingByPieceRepository->findByMaterialId($materialId) as $row) {
            if ($matchOption($row)) {
                $this->deleteMaterialPricingByPieceHandler->handle(
                    new DeleteMaterialPricingByPieceCommand(id: (int) $row->id)
                );
            }
        }
        foreach ($this->materialPricingCutRepository->findByMaterialId($materialId) as $row) {
            if ($matchOption($row)) {
                $this->deleteMaterialPricingCutHandler->handle(
                    new DeleteMaterialPricingCutCommand(id: (int) $row->id)
                );
            }
        }
        foreach ($this->materialProcessingRepository->findByMaterialId($materialId) as $row) {
            if ($matchOption($row)) {
                $this->deleteMaterialProcessingHandler->handle(
                    new DeleteMaterialProcessingCommand(id: (int) $row->id)
                );
            }
        }
    }
}
