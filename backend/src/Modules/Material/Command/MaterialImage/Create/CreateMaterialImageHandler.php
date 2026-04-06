<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Create;

use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\FileUploaderService;
use App\Components\Storage\ImageFileValidator;
use App\Modules\Material\Entity\MaterialImage\Fields\Enums\MaterialImageDirectory;
use App\Modules\Material\Entity\MaterialImage\MaterialImage;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use Random\RandomException;

final readonly class CreateMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private FileUploaderService $uploader,
        private ImageFileValidator $fileValidator,
        private FlusherInterface $flusher,
    ) {}

    /**
     * @throws RandomException
     */
    public function handle(CreateMaterialImageCommand $command): void
    {
        $path = $this->uploader->upload(
            tmpFilePath: $command->tmpFilePath,
            destinationDir: MaterialImageDirectory::MATERIAL->value,
            validator: $this->fileValidator,
        );

        $image = MaterialImage::create(
            materialId: $command->materialId,
            path: $path,
            alt: $command->alt,
        );

        $this->materialImageRepository->add($image);
        $this->flusher->flush();
    }
}
