<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Update;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Command\MaterialImage\Create\CreateMaterialImageCommand;
use App\Modules\Material\Command\MaterialImage\Create\CreateMaterialImageHandler;
use App\Modules\Material\Command\MaterialImage\Update\UpdateMaterialImageCommand;
use App\Modules\Material\Command\MaterialImage\Update\UpdateMaterialImageHandler;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use Random\RandomException;

final readonly class UpdateMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialPermissionService $materialPermissionService,
        private CreateMaterialImageHandler $createMaterialImageHandler,
        private UpdateMaterialImageHandler $updateMaterialImageHandler,
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

        if ($command->imageId !== null) {
            $this->updateImage(
                imageId: $command->imageId,
                tmpFilePath: $command->tmpFilePath,
                imageAlt: $command->imageAlt,
            );
        } elseif ($command->tmpFilePath !== null) {
            $this->uploadImage(
                materialId: $command->materialId,
                tmpFilePath: $command->tmpFilePath,
                imageAlt: $command->imageAlt,
            );
        }

        $this->cacher->delete('material_by_id_' . $command->materialId);

        $this->flusher->flush();
    }

    /**
     * @throws RandomException
     */
    private function uploadImage(int $materialId, string $tmpFilePath, ?string $imageAlt): void
    {
        $this->createMaterialImageHandler->handle(new CreateMaterialImageCommand(
            materialId: $materialId,
            tmpFilePath: $tmpFilePath,
            alt: $imageAlt,
        ));
    }

    private function updateImage(int $imageId, ?string $tmpFilePath, ?string $imageAlt): void
    {
        $this->updateMaterialImageHandler->handle(new UpdateMaterialImageCommand(
            id: $imageId,
            tmpFilePath: $tmpFilePath,
            alt: $imageAlt,
        ));
    }
}
