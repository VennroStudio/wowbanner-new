<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Create;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Command\MaterialImage\Create\CreateMaterialImageCommand;
use App\Modules\Material\Command\MaterialImage\Create\CreateMaterialImageHandler;
use App\Modules\Material\Entity\Material\Material;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use RuntimeException;

final readonly class CreateMaterialHandler
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialPermissionService $materialPermissionService,
        private CreateMaterialImageHandler $createMaterialImageHandler,
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

        if ($material->id === null) {
            throw new RuntimeException('Material ID is null after creation.');
        }

        foreach ($command->images as $image) {
            $this->uploadImage(
                materialId: $material->id,
                tmpFilePath: $image->file->getPath(),
                imageAlt: $image->alt,
            );
        }
    }

    private function uploadImage(int $materialId, string $tmpFilePath, ?string $imageAlt): void
    {
        $this->createMaterialImageHandler->handle(new CreateMaterialImageCommand(
            materialId: $materialId,
            tmpFilePath: $tmpFilePath,
            alt: $imageAlt,
        ));
    }
}

