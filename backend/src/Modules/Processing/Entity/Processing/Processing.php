<?php

declare(strict_types=1);

namespace App\Modules\Processing\Entity\Processing;

use App\Modules\Processing\Entity\Processing\Fields\Enums\ProcessingType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'processings')]
class Processing
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $name;

    #[ORM\Column(type: Types::TEXT)]
    private(set) string $description;

    #[ORM\Column(type: Types::INTEGER, enumType: ProcessingType::class)]
    private(set) ProcessingType $type;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $costPrice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $price;

    private function __construct(
        string $name,
        string $description,
        ProcessingType $type,
        string $costPrice,
        string $price,
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->costPrice = $costPrice;
        $this->price = $price;
    }

    public static function create(
        string $name,
        string $description,
        ProcessingType $type,
        string $costPrice,
        string $price,
    ): self {
        return new self($name, $description, $type, $costPrice, $price);
    }

    public function edit(
        string $name,
        string $description,
        ProcessingType $type,
        string $costPrice,
        string $price,
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->costPrice = $costPrice;
        $this->price = $price;
    }
}
