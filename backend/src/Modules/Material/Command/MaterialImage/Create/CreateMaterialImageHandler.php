<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Create;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\FileUploaderService;
use App\Components\Storage\ImageFileValidator;
use App\Modules\Material\Entity\MaterialImage\Fields\Enums\MaterialImageDirectory;
use App\Modules\Material\Entity\MaterialImage\MaterialImage;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\ReadModel\MaterialImage\MaterialImageItem;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use Random\RandomException;

final readonly class CreateMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private MaterialPermissionService $materialPermissionService,
        private FileUploaderService $uploader,
        private ImageFileValidator $fileValidator,
        private FlusherInterface $flusher,
    ) {}

    /**
     * @throws AccessDeniedException
     * @throws RandomException
     */
    public function handle(CreateMaterialImageCommand $command): void
    {
        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::CREATE,
        );

        /** @var MaterialImageItem $item */
        foreach ($command->images as $item) {
            $path = $this->uploader->upload(
                tmpFilePath: $item->file->getPath(),
                destinationDir: MaterialImageDirectory::MATERIAL->value,
                validator: $this->fileValidator,
            );

            $image = MaterialImage::create(
                materialId: $command->materialId,
                path: $path,
                alt: $item->alt,
            );

            $this->materialImageRepository->add($image);
        }

        $this->flusher->flush();
    }
}
