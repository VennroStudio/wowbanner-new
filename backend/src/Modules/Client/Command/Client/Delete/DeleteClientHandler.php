<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\Client\Delete;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Client\Command\ClientCompany\Delete\DeleteClientCompanyCommand;
use App\Modules\Client\Command\ClientCompany\Delete\DeleteClientCompanyHandler;
use App\Modules\Client\Command\ClientPhone\Delete\DeleteClientPhoneCommand;
use App\Modules\Client\Command\ClientPhone\Delete\DeleteClientPhoneHandler;
use App\Modules\Client\Entity\Client\ClientRepository;
use App\Modules\Client\Entity\ClientCompany\ClientCompanyRepository;
use App\Modules\Client\Entity\ClientPhone\ClientPhoneRepository;
use App\Modules\Client\Permission\ClientPermission;
use App\Modules\Client\Service\ClientPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeleteClientHandler
{
    public function __construct(
        private ClientRepository $repository,
        private ClientPhoneRepository $phoneRepository,
        private ClientCompanyRepository $companyRepository,
        private FlusherInterface $flusher,
        private ClientPermissionService $permissionService,
        private DeleteClientPhoneHandler $deletePhoneHandler,
        private DeleteClientCompanyHandler $deleteCompanyHandler,
    ) {}

    /**
     * @throws AccessDeniedException
     */
    public function handle(DeleteClientCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ClientPermission::DELETE,
        );

        $client = $this->repository->getById($command->id);

        $this->deletePhones($client->id);
        $this->deleteCompanies($client->id);

        $this->repository->remove($client);
        $this->flusher->flush();
    }

    private function deletePhones(int $clientId): void
    {
        $phones = $this->phoneRepository->findByClientId($clientId);
        foreach ($phones as $phone) {
            $this->deletePhoneHandler->handle(new DeleteClientPhoneCommand($phone->id));
        }
    }

    private function deleteCompanies(int $clientId): void
    {
        $companies = $this->companyRepository->findByClientId($clientId);
        foreach ($companies as $company) {
            $this->deleteCompanyHandler->handle(new DeleteClientCompanyCommand($company->id));
        }
    }
}
