<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Create;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Entity\MaterialImage\MaterialImage;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;

final readonly class CreateMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private FlusherInterface $flusher,
    ) {}

    public function handle(CreateMaterialImageCommand $command): void
    {
        $image = MaterialImage::create(
            materialId: $command->materialId,
            path: $command->path,
            alt: $command->alt,
        );

        $this->materialImageRepository->add($image);
        $this->flusher->flush();
    }
}
