<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Update;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Command\Material\MaterialImageItem;
use App\Modules\Material\Command\MaterialImage\Create\CreateMaterialImageCommand;
use App\Modules\Material\Command\MaterialImage\Create\CreateMaterialImageHandler;
use App\Modules\Material\Command\MaterialImage\Delete\DeleteMaterialImageCommand;
use App\Modules\Material\Command\MaterialImage\Delete\DeleteMaterialImageHandler;
use App\Modules\Material\Entity\Material\Material;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialPermissionService $materialPermissionService,
        private CreateMaterialImageHandler $createMaterialImageHandler,
        private DeleteMaterialImageHandler $deleteMaterialImageHandler,
        private FlusherInterface $flusher,
        private Cacher $cacher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(UpdateMaterialCommand $command): void
    {
        $material = $this->materialRepository->getById($command->materialId);

        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::UPDATE,
        );

        $material->edit(
            name: $command->name,
            description: $command->description,
        );

        foreach ($command->imagesToDelete as $imageId) {
            $this->deleteMaterialImageHandler->handle(new DeleteMaterialImageCommand(
                id: $imageId,
            ));
        }

        foreach ($command->newImages as $image) {
            $this->createMaterialImageHandler->handle(new CreateMaterialImageCommand(
                materialId: $command->materialId,
                tmpFilePath: $image->file->getPath(),
                alt: $image->alt,
            ));
        }

        $this->cacher->delete('material_by_id_' . $command->materialId);

        $this->flusher->flush();
    }
}

