<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Delete;

use App\Components\Flusher\FlusherInterface;
use App\Components\Storage\StorageInterface;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;

final readonly class DeleteMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private StorageInterface $storage,
        private FlusherInterface $flusher,
    ) {}

    public function handle(DeleteMaterialImageCommand $command): void
    {
        if ($command->id !== null) {
            $this->remove($command->id);
        }

        if ($command->materialId !== null) {
            $images = $this->materialImageRepository->findByMaterialId($command->materialId);
            foreach ($images as $image) {
                $this->remove($image->id);
            }
        }

        $this->flusher->flush();
    }

    private function remove(int $id): void
    {
        $image = $this->materialImageRepository->getById($id);

        $this->storage->delete($image->path);
        $this->materialImageRepository->remove($image);
    }
}
