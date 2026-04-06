<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Create;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\FileUploaderService;
use App\Components\Storage\ImageFileValidator;
use App\Modules\Material\Command\MaterialImage\Create\CreateMaterialImageCommand;
use App\Modules\Material\Command\MaterialImage\Create\CreateMaterialImageHandler;
use App\Modules\Material\Entity\Material\Material;
use App\Modules\Material\Entity\Material\MaterialRepository;
use App\Modules\Material\Entity\MaterialImage\Fields\Enums\MaterialImageDirectory;
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
        private FileUploaderService $uploader,
        private ImageFileValidator $fileValidator,
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

        if ($command->tmpFilePath !== null) {
            $this->uploadImage(
                materialId: $material->id,
                tmpFilePath: $command->tmpFilePath,
                imageAlt: $command->imageAlt,
            );
        }
    }

    private function uploadImage(int $materialId, string $tmpFilePath, ?string $imageAlt): void
    {
        $path = $this->uploader->upload(
            tmpFilePath: $tmpFilePath,
            destinationDir: MaterialImageDirectory::MATERIAL->value,
            validator: $this->fileValidator,
        );

        $this->createMaterialImageHandler->handle(new CreateMaterialImageCommand(
            materialId: $materialId,
            path: $path,
            alt: $imageAlt,
        ));
    }
}
