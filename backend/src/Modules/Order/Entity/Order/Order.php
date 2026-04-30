<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Components\Clock\UtcClock;
use App\Modules\Order\Entity\Order\Fields\Enums\StatusType;
use App\Modules\Order\Entity\Order\Fields\Enums\StorageType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\Index(name: 'idx_order_creator_id', columns: ['creator_id'])]
#[ORM\Index(name: 'idx_order_manager_id', columns: ['manager_id'])]
#[ORM\Index(name: 'idx_order_designer_id', columns: ['designer_id'])]
#[ORM\Index(name: 'idx_order_client_id', columns: ['client_id'])]
#[ORM\Index(name: 'idx_order_status_type', columns: ['status_type'])]
#[ORM\Index(name: 'idx_order_storage_type', columns: ['storage_type'])]
#[ORM\Index(name: 'idx_order_created_at', columns: ['created_at'])]
#[ORM\Index(name: 'idx_order_deadline_at', columns: ['deadline_at'])]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $creatorId;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private(set) ?int $managerId;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private(set) ?int $designerId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $clientId;

    #[ORM\Column(type: Types::INTEGER, enumType: StatusType::class)]
    private(set) StatusType $statusType;

    #[ORM\Column(type: Types::INTEGER, enumType: StorageType::class)]
    private(set) StorageType $storageType;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $generalNote;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $additionalNote;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    private(set) ?string $extension;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $printPrice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $layoutPrice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $installationPrice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $additionalPrice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $deliveryPrice;

    #[ORM\Column(type: Types::BOOLEAN)]
    private(set) bool $isFile;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $deadlineAt;

    private function __construct(
        int $creatorId,
        ?int $managerId,
        ?int $designerId,
        int $clientId,
        StatusType $statusType,
        StorageType $storageType,
        ?string $generalNote,
        ?string $additionalNote,
        ?string $extension,
        string $printPrice,
        string $layoutPrice,
        string $installationPrice,
        string $additionalPrice,
        string $deliveryPrice,
        bool $isFile,
        DateTimeImmutable $deadlineAt,
    ) {
        $this->creatorId = $creatorId;
        $this->managerId = $managerId;
        $this->designerId = $designerId;
        $this->clientId = $clientId;
        $this->statusType = $statusType;
        $this->storageType = $storageType;
        $this->generalNote = $generalNote;
        $this->additionalNote = $additionalNote;
        $this->extension = $extension;
        $this->printPrice = $printPrice;
        $this->layoutPrice = $layoutPrice;
        $this->installationPrice = $installationPrice;
        $this->additionalPrice = $additionalPrice;
        $this->deliveryPrice = $deliveryPrice;
        $this->isFile = $isFile;
        $this->deadlineAt = $deadlineAt;
        $this->createdAt = UtcClock::now();
    }

    public static function create(
        int $creatorId,
        ?int $managerId,
        ?int $designerId,
        int $clientId,
        StatusType $statusType,
        StorageType $storageType,
        ?string $generalNote,
        ?string $additionalNote,
        ?string $extension,
        string $printPrice,
        string $layoutPrice,
        string $installationPrice,
        string $additionalPrice,
        string $deliveryPrice,
        bool $isFile,
        DateTimeImmutable $deadlineAt,
    ): self {
        return new self(
            creatorId: $creatorId,
            managerId: $managerId,
            designerId: $designerId,
            clientId: $clientId,
            statusType: $statusType,
            storageType: $storageType,
            generalNote: $generalNote,
            additionalNote: $additionalNote,
            extension: $extension,
            printPrice: $printPrice,
            layoutPrice: $layoutPrice,
            installationPrice: $installationPrice,
            additionalPrice: $additionalPrice,
            deliveryPrice: $deliveryPrice,
            isFile: $isFile,
            deadlineAt: $deadlineAt,
        );
    }

    public function edit(
        ?int $managerId,
        ?int $designerId,
        int $clientId,
        StatusType $statusType,
        StorageType $storageType,
        ?string $generalNote,
        ?string $additionalNote,
        ?string $extension,
        string $printPrice,
        string $layoutPrice,
        string $installationPrice,
        string $additionalPrice,
        string $deliveryPrice,
        bool $isFile,
        DateTimeImmutable $deadlineAt,
    ): void {
        $this->managerId = $managerId;
        $this->designerId = $designerId;
        $this->clientId = $clientId;
        $this->statusType = $statusType;
        $this->storageType = $storageType;
        $this->generalNote = $generalNote;
        $this->additionalNote = $additionalNote;
        $this->extension = $extension;
        $this->printPrice = $printPrice;
        $this->layoutPrice = $layoutPrice;
        $this->installationPrice = $installationPrice;
        $this->additionalPrice = $additionalPrice;
        $this->deliveryPrice = $deliveryPrice;
        $this->isFile = $isFile;
        $this->deadlineAt = $deadlineAt;
    }
}
