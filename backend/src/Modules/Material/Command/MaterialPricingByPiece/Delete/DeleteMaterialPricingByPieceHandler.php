<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialPricingByPiece\Delete;

use App\Modules\Material\Entity\MaterialPricingByPiece\MaterialPricingByPieceRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class DeleteMaterialPricingByPieceHandler
{
    public function __construct(
        private MaterialPricingByPieceRepository $repository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(DeleteMaterialPricingByPieceCommand $command): void
    {
        $entity = $this->repository->getById($command->id);
        $materialId = $entity->materialId;
        $optionId = $entity->optionId;

        $this->repository->remove($entity);

        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext($materialId, $optionId);
    }
}
