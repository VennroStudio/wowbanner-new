<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialProcessing;

interface MaterialProcessingRepository
{
    public function add(MaterialProcessing $materialProcessing): void;

    public function remove(MaterialProcessing $materialProcessing): void;

    public function getById(int $id): MaterialProcessing;

    public function findById(int $id): ?MaterialProcessing;
}
