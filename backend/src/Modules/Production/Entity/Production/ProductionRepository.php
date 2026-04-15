<?php

declare(strict_types=1);

namespace App\Modules\Production\Entity\Production;

interface ProductionRepository
{
    public function getById(int $id): Production;

    public function findById(int $id): ?Production;

    public function add(Production $production): void;

    public function remove(Production $production): void;
}
