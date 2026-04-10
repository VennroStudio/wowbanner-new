<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientCompany\Delete;

use App\Modules\Client\Entity\ClientCompany\ClientCompanyRepository;

final readonly class DeleteClientCompanyHandler
{
    public function __construct(
        private ClientCompanyRepository $repository,
    ) {}

    public function handle(DeleteClientCompanyCommand $command): void
    {
        $company = $this->repository->getById($command->id);
        $this->repository->remove($company);
    }
}
