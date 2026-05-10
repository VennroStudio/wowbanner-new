<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderFile;

use App\Components\Clock\UtcClock;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_files')]
#[ORM\Index(name: 'idx_order_file_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_order_file_created_at', columns: ['created_at'])]
class OrderFile
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderId;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $diskPath;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $fileName;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $originalName;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $createdAt;

    private function __construct(
        int $orderId,
        string $diskPath,
        string $fileName,
        string $originalName,
    ) {
        $this->orderId = $orderId;
        $this->diskPath = $diskPath;
        $this->fileName = $fileName;
        $this->originalName = $originalName;
        $this->createdAt = UtcClock::now();
    }

    public static function create(
        int $orderId,
        string $diskPath,
        string $fileName,
        string $originalName,
    ): self {
        return new self(
            orderId: $orderId,
            diskPath: $diskPath,
            fileName: $fileName,
            originalName: $originalName,
        );
    }
}
