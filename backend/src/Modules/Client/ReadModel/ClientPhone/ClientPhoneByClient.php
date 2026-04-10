<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientPhone;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Client\Entity\ClientPhone\Fields\PhoneType;
use App\Modules\Client\ReadModel\ClientPhone\Interface\ClientPhoneModelInterface;
use Override;

final readonly class ClientPhoneByClient implements ClientPhoneModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $clientId,
        public PhoneType $type,
        public string $phone,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     client_id: int,
     *     type: int,
     *     phone: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            clientId: (int)$row['client_id'],
            type: PhoneType::from((int)$row['type']),
            phone: $row['phone'],
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
            'id'        => $this->id,
            'client_id' => $this->clientId,
            'type'      => ['id' => $this->type->value, 'label' => $this->type->getLabel()],
            'phone'     => $this->phone,
        ];
    }
}
