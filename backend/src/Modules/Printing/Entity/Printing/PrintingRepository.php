<?php

declare(strict_types=1);

namespace App\Modules\Printing\Entity\Printing;

interface PrintingRepository
{
    public function add(Printing $printing): void;

    public function remove(Printing $printing): void;

    public function getById(int $id): Printing;

    public function findById(int $id): ?Printing;
}
