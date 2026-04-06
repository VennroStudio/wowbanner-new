<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Command\Gallery\Delete;

use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\StorageInterface;
use App\Modules\Gallery\Entity\Gallery\GalleryRepository;

final readonly class DeleteGalleryHandler
{
    public function __construct(
        private GalleryRepository $galleryRepository,
        private StorageInterface $storage,
        private FlusherInterface $flusher,
    ) {}

    public function handle(DeleteGalleryCommand $command): void
    {
        $image = $this->galleryRepository->getById($command->galleryId);

        $this->storage->delete($image->path);

        $this->galleryRepository->remove($image);

        $this->flusher->flush();
    }
}
