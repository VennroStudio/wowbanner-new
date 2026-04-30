<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderSection;

use App\Modules\Order\Entity\OrderSection\Fields\Enums\SectionType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_sections')]
#[ORM\Index(name: 'idx_order_section_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_order_section_type', columns: ['section_type'])]
class OrderSection
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderId;

    #[ORM\Column(type: Types::INTEGER, enumType: SectionType::class)]
    private(set) SectionType $sectionType;

    private function __construct(int $orderId, SectionType $sectionType)
    {
        $this->orderId = $orderId;
        $this->sectionType = $sectionType;
    }

    public static function create(int $orderId, SectionType $sectionType): self
    {
        return new self($orderId, $sectionType);
    }

    public function edit(SectionType $sectionType): void
    {
        $this->sectionType = $sectionType;
    }
}
