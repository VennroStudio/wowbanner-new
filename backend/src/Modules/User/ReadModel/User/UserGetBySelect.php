<?php

declare(strict_types=1);

namespace App\Modules\User\ReadModel\User;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use App\Modules\User\Entity\User\Fields\Enums\UserStatus;
use App\Modules\User\ReadModel\User\Interface\UserModelInterface;
use Override;

final readonly class UserGetBySelect implements UserModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public UserRole $role,
        public UserStatus $status,
        public string $firstName,
        public string $lastName,
        public string $email,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     role: int,
     *     status: int,
     *     first_name: string,
     *     last_name: string,
     *     email: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            role: UserRole::from((int) $row['role']),
            status: UserStatus::from((int) $row['status']),
            firstName: $row['first_name'],
            lastName: $row['last_name'],
            email: $row['email'],
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
            'name' => trim($this->lastName . ' ' . $this->firstName),
            'role' => [
                'id' => $this->role->value,
                'label' => $this->role->getLabel(),
            ],
            'status' => [
                'id' => $this->status->value,
                'label' => $this->status->getLabel(),
            ],
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ];
    }
}
