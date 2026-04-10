<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientCompany;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'client_companies')]
#[ORM\Index(columns: ['client_id'], name: 'idx_client_id')]
final class ClientCompany
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $clientId;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $companyName;

    private function __construct(int $clientId, string $companyName)
    {
        $this->clientId = $clientId;
        $this->companyName = $companyName;
    }

    public static function create(int $clientId, string $companyName): self
    {
        return new self($clientId, $companyName);
    }

    public function edit(string $companyName): void
    {
        $this->companyName = $companyName;
    }
}
