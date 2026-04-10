<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\Client;

use App\Components\Clock\UtcClock;
use App\Modules\Client\Entity\Client\Fields\ClientType;
use App\Modules\Client\Entity\Client\Fields\Docs;
use App\Modules\Client\Entity\ClientCompany\ClientCompany;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'clients')]
#[ORM\Index(columns: ['email'], name: 'idx_email')]
#[ORM\Index(columns: ['last_name'], name: 'idx_last_name')]
#[ORM\Index(columns: ['type'], name: 'idx_type')]
#[ORM\Index(columns: ['created_at'], name: 'idx_created_at')]
final class Client
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private(set) ?string $oldFullName = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $lastName;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $firstName;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private(set) ?string $middleName;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private(set) ?string $email;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $info;

    #[ORM\Column(type: Types::SMALLINT, enumType: Docs::class)]
    private(set) Docs $docs;

    #[ORM\Column(type: Types::SMALLINT, enumType: ClientType::class)]
    private(set) ClientType $type;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private(set) DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private(set) ?DateTimeImmutable $updatedAt = null;

    private function __construct(
        string $lastName,
        string $firstName,
        ?string $middleName,
        ?string $email,
        Docs $docs,
        ClientType $type,
        ?string $info = null,
        ?string $oldFullName = null,
    ) {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->email = $email;
        $this->docs = $docs;
        $this->type = $type;
        $this->info = $info;
        $this->oldFullName = $oldFullName;
        $this->createdAt = UtcClock::now();
    }

    public static function create(
        string $lastName,
        string $firstName,
        ?string $middleName,
        ?string $email,
        Docs $docs,
        ClientType $type,
        ?string $info = null,
        ?string $oldFullName = null,
    ): self {
        return new self(
            lastName: $lastName,
            firstName: $firstName,
            middleName: $middleName,
            email: $email,
            docs: $docs,
            type: $type,
            info: $info,
            oldFullName: $oldFullName,
        );
    }

    public function edit(
        string $lastName,
        string $firstName,
        ?string $middleName,
        ?string $email,
        Docs $docs,
        ClientType $type,
        ?string $info = null,
    ): void {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->email = $email;
        $this->docs = $docs;
        $this->type = $type;
        $this->info = $info;
        $this->updatedAt = UtcClock::now();
    }

    public function getFullName(): string
    {
        $parts = array_filter([
            $this->lastName,
            $this->firstName,
            $this->middleName,
        ]);

        return implode(' ', $parts);
    }
}
