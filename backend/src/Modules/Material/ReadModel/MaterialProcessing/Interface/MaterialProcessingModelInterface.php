<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialProcessing\Interface;

interface MaterialProcessingModelInterface
{
    public function getId(): int;

    public function getMaterialId(): int;

    public function getOptionId(): int;

    public function getProcessingId(): int;

    /**
     * @return array{
     *     id: int,
     *     material_id: int,
     *     option_id: int,
     *     processing_id: int
     * }
     */
    public function toArray(): array;
}
