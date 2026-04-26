<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialOption;

interface MaterialOptionRepository
{
    public function add(MaterialOption $materialOption): void;

    public function remove(MaterialOption $materialOption): void;

    public function getById(int $id): MaterialOption;

    public function findById(int $id): ?MaterialOption;
}
