<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\Client\Create;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Client\Command\ClientCompany\Create\CreateClientCompanyCommand;
use App\Modules\Client\Command\ClientCompany\Create\CreateClientCompanyHandler;
use App\Modules\Client\Command\ClientPhone\Create\CreateClientPhoneCommand;
use App\Modules\Client\Command\ClientPhone\Create\CreateClientPhoneHandler;
use App\Modules\Client\Entity\Client\Client;
use App\Modules\Client\Entity\Client\ClientRepository;
use App\Modules\Client\Entity\Client\Fields\ClientType;
use App\Modules\Client\Entity\Client\Fields\Docs;
use App\Modules\Client\Entity\ClientPhone\Fields\PhoneType;
use App\Modules\Client\Permission\ClientPermission;
use App\Modules\Client\ReadModel\ClientCompany\ClientCompanyItem;
use App\Modules\Client\ReadModel\ClientPhone\ClientPhoneItem;
use App\Modules\Client\Service\ClientPermissionService;
use App\Modules\Client\Service\ClientValidatorService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class CreateClientHandler
{
    public function __construct(
        private ClientRepository $repository,
        private FlusherInterface $flusher,
        private ClientPermissionService $permissionService,
        private ClientValidatorService $validator,
        private CreateClientPhoneHandler $phoneHandler,
        private CreateClientCompanyHandler $companyHandler,
    ) {}

    public function handle(CreateClientCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ClientPermission::CREATE,
        );

        $phones = $this->validator->normalizePhones($command->phones);

        $this->validator->validate(
            email: $command->email,
            type: $command->type,
            phones: $phones,
            companies: $command->companies,
        );

        $client = Client::create(
            lastName: $command->lastName,
            firstName: $command->firstName,
            middleName: $command->middleName,
            email: $command->email,
            docs: Docs::from($command->docs),
            type: ClientType::from($command->type),
            info: $command->info,
        );

        $this->repository->add($client);

        $this->flusher->flush();

        $this->processPhones($client, $phones);
        $this->processCompanies($client, $command->companies);

        $this->flusher->flush();
    }

    /**
     * @param list<ClientPhoneItem> $phones
     */
    private function processPhones(Client $client, array $phones): void
    {
        foreach ($phones as $item) {
            $this->phoneHandler->handle(new CreateClientPhoneCommand(
                clientId: (int)$client->id,
                type: PhoneType::from($item->type),
                phone: $item->phone,
            ));
        }
    }

    /**
     * @param list<ClientCompanyItem> $companies
     */
    private function processCompanies(Client $client, array $companies): void
    {
        foreach ($companies as $item) {
            $this->companyHandler->handle(new CreateClientCompanyCommand(
                clientId: (int)$client->id,
                companyName: $item->name,
            ));
        }
    }
}
