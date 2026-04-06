<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Create;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Entity\Material\Material;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class CreateMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialPermissionService $materialPermissionService,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(CreateMaterialCommand $command): void
    {
        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::CREATE,
        );

        $material = Material::create(
            name: $command->name,
            description: $command->description,
        );

        $this->materialRepository->add($material);
        $this->flusher->flush();
    }
}
