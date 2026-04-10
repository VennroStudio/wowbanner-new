<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientCompany\Update;

use App\Modules\Client\Entity\ClientCompany\ClientCompanyRepository;

final readonly class UpdateClientCompanyHandler
{
    public function __construct(
        private ClientCompanyRepository $repository,
    ) {}

    public function handle(UpdateClientCompanyCommand $command): void
    {
        $company = $this->repository->getById($command->id);
        $company->edit(
            companyName: $command->companyName,
        );
    }
}
