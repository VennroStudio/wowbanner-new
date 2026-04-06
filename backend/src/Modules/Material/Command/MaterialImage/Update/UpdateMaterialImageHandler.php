<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Update;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;

final readonly class UpdateMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private FlusherInterface $flusher,
    ) {}

    public function handle(UpdateMaterialImageCommand $command): void
    {
        $image = $this->materialImageRepository->getById($command->id);

        $image->edit($command->alt);

        $this->flusher->flush();
    }
}
