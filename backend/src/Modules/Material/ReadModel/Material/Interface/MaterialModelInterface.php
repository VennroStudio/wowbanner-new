<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\Material\Interface;

interface MaterialModelInterface
{
    public function getId(): int;

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     description: string
     * }
     */
    public function toArray(): array;
}
