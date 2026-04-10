<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientCompany;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Client\ReadModel\ClientCompany\Interface\ClientCompanyModelInterface;
use Override;

final readonly class ClientCompanyByClient implements ClientCompanyModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $clientId,
        public string $companyName,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     client_id: int,
     *     company_name: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            clientId: (int)$row['client_id'],
            companyName: $row['company_name'],
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
            'id'           => $this->id,
            'client_id'    => $this->clientId,
            'company_name' => $this->companyName,
        ];
    }
}
