<?php

declare(strict_types=1);

namespace App\Modules\Processing\ReadModel\Processing;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Processing\ReadModel\Processing\Interface\ProcessingModelInterface;
use Override;

final readonly class ProcessingGetBySelect implements ProcessingModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public string $name,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     name: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
