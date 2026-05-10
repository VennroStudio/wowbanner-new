<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\Order;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\Entity\Order\Fields\Enums\StatusType;
use App\Modules\Order\Entity\Order\Fields\Enums\StorageType;
use App\Modules\Order\ReadModel\Order\Interface\OrderModelInterface;
use Override;

final readonly class OrderById implements OrderModelInterface
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
        public ?string $additionalNote,
        public ?string $extension,
        public string $printPrice,
        public string $layoutPrice,
        public string $installationPrice,
        public string $additionalPrice,
        public string $deliveryPrice,
        public string $createdAt,
        public string $acceptedAt,
        public string $deadlineAt,
    ) {}

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
     *     additional_note: string|null,
     *     extension: string|null,
     *     print_price: string,
     *     layout_price: string,
     *     installation_price: string,
     *     additional_price: string,
     *     delivery_price: string,
     *     created_at: string,
     *     accepted_at: string,
     *     deadline_at: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            creatorId: (int) $row['creator_id'],
            managerId: $row['manager_id'] !== null ? (int) $row['manager_id'] : null,
            designerId: $row['designer_id'] !== null ? (int) $row['designer_id'] : null,
            clientId: (int) $row['client_id'],
            statusType: StatusType::from((int) $row['status_type']),
            storageType: StorageType::from((int) $row['storage_type']),
            generalNote: $row['general_note'],
            additionalNote: $row['additional_note'],
            extension: $row['extension'],
            printPrice: $row['print_price'],
            layoutPrice: $row['layout_price'],
            installationPrice: $row['installation_price'],
            additionalPrice: $row['additional_price'],
            deliveryPrice: $row['delivery_price'],
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
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'creator_id' => $this->creatorId,
            'manager_id' => $this->managerId,
            'designer_id' => $this->designerId,
            'client_id' => $this->clientId,
            'status_type' => ['id' => $this->statusType->value, 'label' => $this->statusType->getLabel()],
            'storage_type' => ['id' => $this->storageType->value, 'label' => $this->storageType->getLabel()],
            'general_note' => $this->generalNote,
            'additional_note' => $this->additionalNote,
            'extension' => $this->extension,
            'print_price' => $this->printPrice,
            'layout_price' => $this->layoutPrice,
            'installation_price' => $this->installationPrice,
            'additional_price' => $this->additionalPrice,
            'delivery_price' => $this->deliveryPrice,
            'created_at' => $this->createdAt,
            'accepted_at' => $this->acceptedAt,
            'deadline_at' => $this->deadlineAt,
        ];
    }
}
