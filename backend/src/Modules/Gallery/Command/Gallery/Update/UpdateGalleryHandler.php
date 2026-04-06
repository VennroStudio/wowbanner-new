<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Command\Gallery\Update;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Gallery\Entity\Gallery\GalleryRepository;

final readonly class UpdateGalleryHandler
{
    public function __construct(
        private GalleryRepository $galleryRepository,
        private FlusherInterface $flusher,
    ) {}

    public function handle(UpdateGalleryCommand $command): void
    {
        $image = $this->galleryRepository->getById($command->galleryId);

        $newAlt = $command->alt !== '' ? $command->alt : null;
        if ($image->alt !== $newAlt) {
            $image->changeAlt($newAlt);
        }

        $this->flusher->flush();
    }
}
