<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\Order;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\Entity\Order\Fields\Enums\StatusType;
use App\Modules\Order\Entity\Order\Fields\Enums\StorageType;
use App\Modules\Order\ReadModel\Order\Interface\OrderModelInterface;
use Override;

final readonly class OrderDetails implements OrderModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $creatorId,
        public ?int $managerId,
        public ?int $designerId,
        public int $clientId,
        public StatusType $statusType,
        public StorageType $storageType,
        public ?string $generalNote,
        public ?string $extension,
        public string $createdAt,
        public string $acceptedAt,
        public string $deadlineAt,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'           => 'id',
            'creator_id'   => 'creator_id',
            'manager_id'   => 'manager_id',
            'designer_id'  => 'designer_id',
            'client_id'    => 'client_id',
            'status_type'  => 'status_type',
            'storage_type' => 'storage_type',
            'general_note' => 'general_note',
            'extension'    => 'extension',
            'created_at'   => 'created_at',
            'accepted_at'  => 'accepted_at',
            'deadline_at'  => 'deadline_at',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     creator_id: int,
     *     manager_id: int|null,
     *     designer_id: int|null,
     *     client_id: int,
     *     status_type: int,
     *     storage_type: int,
     *     general_note: string|null,
     *     extension: string|null,
     *     created_at: string,
     *     accepted_at: string,
     *     deadline_at: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            creatorId: (int)$row['creator_id'],
            managerId: $row['manager_id'] !== null ? (int)$row['manager_id'] : null,
            designerId: $row['designer_id'] !== null ? (int)$row['designer_id'] : null,
            clientId: (int)$row['client_id'],
            statusType: StatusType::from((int)$row['status_type']),
            storageType: StorageType::from((int)$row['storage_type']),
            generalNote: $row['general_note'],
            extension: $row['extension'],
            createdAt: $row['created_at'],
            acceptedAt: $row['accepted_at'],
            deadlineAt: $row['deadline_at'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getClientId(): int
    {
        return $this->clientId;
    }

    #[Override]
    public function getManagerId(): ?int
    {
        return $this->managerId;
    }

    #[Override]
    public function getDesignerId(): ?int
    {
        return $this->designerId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'creator_id'   => $this->creatorId,
            'manager_id'   => $this->managerId,
            'designer_id'  => $this->designerId,
            'client_id'    => $this->clientId,
            'status_type'  => ['id' => $this->statusType->value, 'label' => $this->statusType->getLabel()],
            'storage_type' => ['id' => $this->storageType->value, 'label' => $this->storageType->getLabel()],
            'general_note' => $this->generalNote,
            'extension'    => $this->extension,
            'created_at'   => $this->createdAt,
            'accepted_at'  => $this->acceptedAt,
            'deadline_at'  => $this->deadlineAt,
        ];
    }
}
