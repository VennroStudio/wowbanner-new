<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\Client;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Client\Entity\Client\Fields\ClientType;
use App\Modules\Client\Entity\Client\Fields\Docs;
use App\Modules\Client\ReadModel\Client\Interface\ClientModelInterface;
use Override;

final readonly class ClientById implements ClientModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public ?string $oldFullName,
        public string $lastName,
        public string $firstName,
        public ?string $middleName,
        public ?string $email,
        public ?string $info,
        public Docs $docs,
        public ClientType $type,
        public string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     old_full_name: string|null,
     *     last_name: string,
     *     first_name: string,
     *     middle_name: string|null,
     *     email: string|null,
     *     info: string|null,
     *     docs: int,
     *     type: int,
     *     created_at: string,
     *     updated_at: string|null
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            oldFullName: $row['old_full_name'],
            lastName: $row['last_name'],
            firstName: $row['first_name'],
            middleName: $row['middle_name'],
            email: $row['email'],
            info: $row['info'],
            docs: Docs::from((int)$row['docs']),
            type: ClientType::from((int)$row['type']),
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at'],
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
            'id'            => $this->id,
            'old_full_name' => $this->oldFullName,
            'last_name'     => $this->lastName,
            'first_name'    => $this->firstName,
            'middle_name'   => $this->middleName,
            'email'         => $this->email,
            'info'          => $this->info,
            'docs'          => ['id' => $this->docs->value, 'label' => $this->docs->getLabel()],
            'type'          => ['id' => $this->type->value, 'label' => $this->type->getLabel()],
            'created_at'    => $this->createdAt,
            'updated_at'    => $this->updatedAt,
        ];
    }
}
