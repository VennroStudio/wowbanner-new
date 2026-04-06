<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Command\Gallery\Create;

use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\FileUploaderService;
use App\Components\Storage\ImageFileValidator;
use App\Modules\Gallery\Entity\Gallery\Fields\Enums\GalleryType;
use App\Modules\Gallery\Entity\Gallery\Gallery;
use App\Modules\Gallery\Entity\Gallery\GalleryRepository;
use DateMalformedStringException;
use Random\RandomException;

final readonly class CreateGalleryHandler
{
    public function __construct(
        private GalleryRepository $galleryRepository,
        private FileUploaderService $uploader,
        private ImageFileValidator $fileValidator,
        private FlusherInterface $flusher,
    ) {}

    /**
     * @throws DateMalformedStringException
     * @throws RandomException
     */
    public function handle(CreateGalleryCommand $command): void
    {
        $storagePath = $this->uploader->upload(
            tmpFilePath: $command->tmpFilePath,
            destinationDir: GalleryType::from($command->type)->getPath($command->id),
            validator: $this->fileValidator,
        );

        $image = Gallery::create(
            type: GalleryType::from($command->type),
            path: $storagePath,
            alt: $command->alt !== '' ? $command->alt : null,
        );

        $this->galleryRepository->add($image);

        $this->flusher->flush();
    }
}
