<?php

declare(strict_types=1);

namespace App\Modules\Processing\Command\ProcessingImage\Create;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\FileUploaderService;
use App\Components\Storage\ImageFileValidator;
use App\Modules\Processing\Entity\Processing\ProcessingRepository;
use App\Modules\Processing\Entity\ProcessingImage\Fields\Enums\ProcessingImageDirectory;
use App\Modules\Processing\Entity\ProcessingImage\ProcessingImage;
use App\Modules\Processing\Entity\ProcessingImage\ProcessingImageRepository;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\Processing\ReadModel\ProcessingImage\ProcessingImageItem;
use App\Modules\Processing\Service\ProcessingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use Random\RandomException;

final readonly class CreateProcessingImageHandler
{
    public function __construct(
        private ProcessingImageRepository $repository,
        private ProcessingRepository $processingRepository,
        private ProcessingPermissionService $permissionService,
        private FileUploaderService $uploader,
        private ImageFileValidator $fileValidator,
        private FlusherInterface $flusher,
    ) {}

    /**
     * @throws AccessDeniedException
     * @throws RandomException
     */
    public function handle(CreateProcessingImageCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ProcessingPermission::UPDATE,
        );

        // Ensure processing entity exists
        $this->processingRepository->getById($command->processingId);

        /** @var ProcessingImageItem $item */
        foreach ($command->images as $item) {
            $path = $this->uploader->upload(
                tmpFilePath: $item->file->getPath(),
                destinationDir: ProcessingImageDirectory::PROCESSING->value,
                validator: $this->fileValidator,
            );

            $image = ProcessingImage::create(
                processingId: $command->processingId,
                path: $path,
                alt: $item->alt,
            );

            $this->repository->add($image);
        }

        $this->flusher->flush();
    }
}
