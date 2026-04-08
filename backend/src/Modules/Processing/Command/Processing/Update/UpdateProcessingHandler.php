<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\Processing\Update;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Processing\Entity\Processing\Fields\Enums\ProcessingType;
use App\Modules\Processing\Entity\Processing\ProcessingRepository;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\Processing\Service\ProcessingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateProcessingHandler
{
    public function __construct(
        private ProcessingRepository $repository,
        private ProcessingPermissionService $permissionService,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(UpdateProcessingCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProcessingPermission::UPDATE,
        );

        $processing = $this->repository->getById($command->id);

        $processing->edit(
            name: $command->name,
            description: $command->description,
            type: ProcessingType::from($command->type),
            costPrice: $command->costPrice,
            price: $command->price,
        );

        $this->flusher->flush();
    }
}
