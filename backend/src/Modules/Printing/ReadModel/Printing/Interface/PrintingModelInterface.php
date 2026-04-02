<?php

declare(strict_types=1);

namespace App\Modules\Printing\ReadModel\Printing\Interface;

interface PrintingModelInterface
{
    public function getId(): int;

    /**
     * @return array{
     *     id: int,
     *     name: string
     * }
     */
    public function toArray(): array;
}
