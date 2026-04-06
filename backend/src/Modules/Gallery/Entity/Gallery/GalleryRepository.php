<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Entity\Gallery;

interface GalleryRepository
{
    public function add(Gallery $gallery): void;

    public function remove(Gallery $gallery): void;

    public function getById(int $id): Gallery;

    public function findById(int $id): ?Gallery;
}
