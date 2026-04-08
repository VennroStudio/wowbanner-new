<?php

declare(strict_types=1);

namespace App\Modules\Processing\Entity\ProcessingImage;

interface ProcessingImageRepository
{
    public function add(ProcessingImage $image): void;

    public function remove(ProcessingImage $image): void;

    public function getById(int $id): ProcessingImage;

    public function findById(int $id): ?ProcessingImage;

    /**
     * @return ProcessingImage[]
     */
    public function findByProcessingId(int $processingId): array;
}
