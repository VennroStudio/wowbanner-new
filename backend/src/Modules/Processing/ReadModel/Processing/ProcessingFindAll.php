<?php

declare(strict_types=1);

namespace App\Modules\Processing\ReadModel\Processing;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Processing\Entity\Processing\Fields\Enums\ProcessingType;
use App\Modules\Processing\ReadModel\Processing\Interface\ProcessingModelInterface;
use Override;

final readonly class ProcessingFindAll implements ProcessingModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public ProcessingType $type,
        public string $costPrice,
        public string $price,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     description: string,
     *     type: int,
     *     cost_price: string,
     *     price: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            name: $row['name'],
            description: $row['description'],
            type: ProcessingType::from($row['type']),
            costPrice: $row['cost_price'],
            price: $row['price'],
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
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'type'        => [
                'id'    => $this->type->value,
                'label' => $this->type->getLabel(),
            ],
            'cost_price'  => $this->costPrice,
            'price'       => $this->price,
        ];
    }
}
