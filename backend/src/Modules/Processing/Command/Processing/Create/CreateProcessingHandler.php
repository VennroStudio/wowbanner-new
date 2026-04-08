<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\Processing\Create;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Processing\Entity\Processing\Fields\Enums\ProcessingType;
use App\Modules\Processing\Entity\Processing\Processing;
use App\Modules\Processing\Entity\Processing\ProcessingRepository;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\Processing\Service\ProcessingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class CreateProcessingHandler
{
    public function __construct(
        private ProcessingRepository $repository,
        private ProcessingPermissionService $permissionService,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(CreateProcessingCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProcessingPermission::CREATE,
        );

        $processing = Processing::create(
            name: $command->name,
            description: $command->description,
            type: ProcessingType::from($command->type),
            costPrice: $command->costPrice,
            price: $command->price,
        );

        $this->repository->add($processing);
        $this->flusher->flush();
    }
}
