<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialImage;

interface MaterialImageRepository
{
    public function add(MaterialImage $image): void;

    public function remove(MaterialImage $image): void;

    public function getById(int $id): MaterialImage;

    public function findById(int $id): ?MaterialImage;

    /**
     * @return MaterialImage[]
     */
    public function findByMaterialId(int $materialId): array;
}
