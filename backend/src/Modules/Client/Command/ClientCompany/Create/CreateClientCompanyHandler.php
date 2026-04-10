<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientCompany\Create;

use App\Modules\Client\Entity\ClientCompany\ClientCompany;
use App\Modules\Client\Entity\ClientCompany\ClientCompanyRepository;

final readonly class CreateClientCompanyHandler
{
    public function __construct(
        private ClientCompanyRepository $repository,
    ) {}

    public function handle(CreateClientCompanyCommand $command): void
    {
        $company = ClientCompany::create(
            clientId: $command->clientId,
            companyName: $command->companyName,
        );
        $this->repository->add($company);
    }
}
