<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderSection;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\Entity\OrderSection\Fields\Enums\SectionType;
use App\Modules\Order\ReadModel\OrderSection\Interface\OrderSectionModelInterface;
use Override;

final readonly class OrderSectionDetails implements OrderSectionModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderId,
        public SectionType $sectionType,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'           => 'id',
            'order_id'     => 'order_id',
            'section_type' => 'section_type',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     order_id: int,
     *     section_type: int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            orderId: (int)$row['order_id'],
            sectionType: SectionType::from((int)$row['section_type']),
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
            'id'           => $this->id,
            'section_type' => ['id' => $this->sectionType->value, 'label' => $this->sectionType->getLabel()],
        ];
    }
}
