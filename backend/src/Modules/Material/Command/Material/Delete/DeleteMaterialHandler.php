<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Delete;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\StorageInterface;
use App\Modules\Material\Command\MaterialOption\Delete\DeleteMaterialOptionCommand;
use App\Modules\Material\Command\MaterialOption\Delete\DeleteMaterialOptionHandler;
use App\Modules\Material\Command\MaterialPricingByArea\Delete\DeleteMaterialPricingByAreaCommand;
use App\Modules\Material\Command\MaterialPricingByArea\Delete\DeleteMaterialPricingByAreaHandler;
use App\Modules\Material\Command\MaterialPricingByPiece\Delete\DeleteMaterialPricingByPieceCommand;
use App\Modules\Material\Command\MaterialPricingByPiece\Delete\DeleteMaterialPricingByPieceHandler;
use App\Modules\Material\Command\MaterialPricingCut\Delete\DeleteMaterialPricingCutCommand;
use App\Modules\Material\Command\MaterialPricingCut\Delete\DeleteMaterialPricingCutHandler;
use App\Modules\Material\Command\MaterialProcessing\Delete\DeleteMaterialProcessingCommand;
use App\Modules\Material\Command\MaterialProcessing\Delete\DeleteMaterialProcessingHandler;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use App\Modules\Material\Entity\MaterialPricingByArea\MaterialPricingByAreaRepository;
use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;
use App\Modules\Material\Entity\MaterialPricingCut\MaterialPricingCutRepository;
use App\Modules\Material\Entity\MaterialProcessing\MaterialProcessingRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialImageRepository $materialImageRepository,
        private MaterialPricingByAreaRepository $materialPricingByAreaRepository,
        private MaterialPricingByPieceRepository $materialPricingByPieceRepository,
        private MaterialPricingCutRepository $materialPricingCutRepository,
        private MaterialProcessingRepository $materialProcessingRepository,
        private MaterialOptionRepository $materialOptionRepository,
        private DeleteMaterialPricingByAreaHandler $deleteMaterialPricingByAreaHandler,
        private DeleteMaterialPricingByPieceHandler $deleteMaterialPricingByPieceHandler,
        private DeleteMaterialPricingCutHandler $deleteMaterialPricingCutHandler,
        private DeleteMaterialProcessingHandler $deleteMaterialProcessingHandler,
        private DeleteMaterialOptionHandler $deleteMaterialOptionHandler,
        private MaterialPermissionService $materialPermissionService,
        private StorageInterface $storage,
        private FlusherInterface $flusher,
        private Cacher $cacher,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(DeleteMaterialCommand $command): void
    {
        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::DELETE,
        );

        $material = $this->materialRepository->getById($command->materialId);
        $materialId = $command->materialId;

        $this->deletePricingByArea($materialId);
        $this->deletePricingByPiece($materialId);
        $this->deletePricingCut($materialId);
        $this->deleteProcessing($materialId);
        $this->deleteOptions($materialId);

        $images = $this->materialImageRepository->findByMaterialId($materialId);

        foreach ($images as $image) {
            $this->storage->delete($image->path);
            $this->materialImageRepository->remove($image);
        }

        $this->materialRepository->remove($material);

        $this->cacher->delete('material_by_id_' . $materialId);
        $this->materialQueryCacheInvalidator->invalidateByMaterialId($materialId);

        $this->flusher->flush();
    }

    private function deletePricingByArea(int $materialId): void
    {
        foreach ($this->materialPricingByAreaRepository->findByMaterialId($materialId) as $row) {
            $this->deleteMaterialPricingByAreaHandler->handle(
                new DeleteMaterialPricingByAreaCommand(id: (int) $row->id)
            );
        }
    }

    private function deletePricingByPiece(int $materialId): void
    {
        foreach ($this->materialPricingByPieceRepository->findByMaterialId($materialId) as $row) {
            $this->deleteMaterialPricingByPieceHandler->handle(
                new DeleteMaterialPricingByPieceCommand(id: (int) $row->id)
            );
        }
    }

    private function deletePricingCut(int $materialId): void
    {
        foreach ($this->materialPricingCutRepository->findByMaterialId($materialId) as $row) {
            $this->deleteMaterialPricingCutHandler->handle(
                new DeleteMaterialPricingCutCommand(id: (int) $row->id)
            );
        }
    }

    private function deleteProcessing(int $materialId): void
    {
        foreach ($this->materialProcessingRepository->findByMaterialId($materialId) as $row) {
            $this->deleteMaterialProcessingHandler->handle(
                new DeleteMaterialProcessingCommand(id: (int) $row->id)
            );
        }
    }

    private function deleteOptions(int $materialId): void
    {
        foreach ($this->materialOptionRepository->findByMaterialId($materialId) as $row) {
            $this->deleteMaterialOptionHandler->handle(
                new DeleteMaterialOptionCommand(id: (int) $row->id)
            );
        }
    }
}
