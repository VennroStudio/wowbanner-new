<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Update;

use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\FileUploaderService;
use App\Components\Storage\ImageFileValidator;
use App\Components\Storage\StorageInterface;
use App\Modules\Material\Entity\MaterialImage\Fields\Enums\MaterialImageDirectory;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;

final readonly class UpdateMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private FileUploaderService $uploader,
        private ImageFileValidator $fileValidator,
        private StorageInterface $storage,
        private FlusherInterface $flusher,
    ) {}

    public function handle(UpdateMaterialImageCommand $command): void
    {
        $image = $this->materialImageRepository->getById($command->id);
        $newPath = null;

        if ($command->tmpFilePath !== null) {
            $this->storage->delete($image->path);
            $newPath = $this->uploader->upload(
                tmpFilePath: $command->tmpFilePath,
                destinationDir: MaterialImageDirectory::MATERIAL->value,
                validator: $this->fileValidator,
            );
        }

        $image->edit($newPath, $command->alt);

        $this->flusher->flush();
    }
}
