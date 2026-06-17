<?php

declare(strict_types=1);

namespace App\Modules\Processing\ReadModel\ProcessingImage;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Processing\ReadModel\ProcessingImage\Interface\ProcessingImageModelInterface;
use Override;

final readonly class ProcessingImageByProcessing implements ProcessingImageModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $processingId,
        public string $path,
        public ?string $alt,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'            => 'id',
            'processing_id' => 'processing_id',
            'path'          => 'path',
            'alt'           => 'alt',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     processing_id: int,
     *     path: string,
     *     alt: string|null
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            processingId: (int)$row['processing_id'],
            path: $row['path'],
            alt: $row['alt'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getProcessingId(): int
    {
        return $this->processingId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'path' => $this->path,
            'alt'  => $this->alt,
        ];
    }
}
