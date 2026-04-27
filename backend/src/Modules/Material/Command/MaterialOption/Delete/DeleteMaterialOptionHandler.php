<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialOption\Delete;

use App\Modules\Material\Entity\MaterialOption\MaterialOptionRepository;
use App\Modules\Material\Service\MaterialQueryCacheInvalidator;

final readonly class DeleteMaterialOptionHandler
{
    public function __construct(
        private MaterialOptionRepository $optionRepository,
        private MaterialQueryCacheInvalidator $materialQueryCacheInvalidator,
    ) {}

    public function handle(DeleteMaterialOptionCommand $command): void
    {
        $option = $this->optionRepository->getById($command->id);
        $materialId = $option->materialId;
        $optionId = $command->id;

        $this->optionRepository->remove($option);

        $this->materialQueryCacheInvalidator->invalidateMaterialOption($optionId, $materialId);
        $this->materialQueryCacheInvalidator->invalidateMaterialAndOptionContext($materialId, $optionId);
    }
}
