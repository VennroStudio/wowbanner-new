<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\Material;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'materials')]
class Material
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $name;

    #[ORM\Column(type: Types::TEXT)]
    private(set) string $description;

    private function __construct(
        string $name,
        string $description,
    ) {
        $this->name = $name;
        $this->description = $description;
    }

    public static function create(
        string $name,
        string $description,
    ): self {
        return new self($name, $description);
    }

    public function edit(string $name, string $description): void
    {
        $this->name = $name;
        $this->description = $description;
    }
}
