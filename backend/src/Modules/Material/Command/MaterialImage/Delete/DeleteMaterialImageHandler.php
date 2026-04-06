<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Delete;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;

final readonly class DeleteMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private FlusherInterface $flusher,
    ) {}

    public function handle(DeleteMaterialImageCommand $command): void
    {
        $image = $this->materialImageRepository->getById($command->id);

        $this->materialImageRepository->remove($image);
        $this->flusher->flush();
    }
}
