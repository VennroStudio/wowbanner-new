<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientPhone;

use App\Modules\Client\Entity\ClientPhone\Fields\PhoneType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'client_phones')]
#[ORM\Index(columns: ['client_id'], name: 'idx_client_id')]
#[ORM\Index(columns: ['phone'], name: 'idx_phone')]
final class ClientPhone
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $clientId;

    #[ORM\Column(type: Types::SMALLINT, enumType: PhoneType::class)]
    private(set) PhoneType $type;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private(set) string $phone;

    private function __construct(int $clientId, PhoneType $type, string $phone)
    {
        $this->clientId = $clientId;
        $this->type = $type;
        $this->phone = $phone;
    }

    public static function create(int $clientId, PhoneType $type, string $phone): self
    {
        return new self($clientId, $type, $phone);
    }

    public function edit(PhoneType $type, string $phone): void
    {
        $this->type = $type;
        $this->phone = $phone;
    }
}
