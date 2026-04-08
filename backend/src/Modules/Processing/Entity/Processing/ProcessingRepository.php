<?php

declare(strict_types=1);

namespace App\Modules\Processing\Entity\Processing;

interface ProcessingRepository
{
    public function add(Processing $processing): void;

    public function getById(int $id): Processing;

    public function findById(int $id): ?Processing;

    public function remove(Processing $processing): void;
}
