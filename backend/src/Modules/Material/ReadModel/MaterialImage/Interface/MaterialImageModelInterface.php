<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialImage\Interface;

interface MaterialImageModelInterface
{
    public function getId(): int;

    public function getMaterialId(): int;

    /**
     * @return array{
     *     id: int,
     *     material_id: int,
     *     path: string,
     *     alt: string|null
     * }
     */
    public function toArray(): array;
}
