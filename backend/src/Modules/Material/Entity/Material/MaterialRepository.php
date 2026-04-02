<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\Material;

interface MaterialRepository
{
    public function add(Material $material): void;

    public function remove(Material $material): void;

    public function getById(int $id): Material;

    public function findById(int $id): ?Material;
}
