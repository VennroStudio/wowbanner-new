<?php

declare(strict_types=1);

namespace App\Modules\Client\Service;

use App\Modules\Client\Command\ClientCompany\Create\CreateClientCompanyCommand;
use App\Modules\Client\Command\ClientCompany\Create\CreateClientCompanyHandler;
use App\Modules\Client\Command\ClientCompany\Delete\DeleteClientCompanyCommand;
use App\Modules\Client\Command\ClientCompany\Delete\DeleteClientCompanyHandler;
use App\Modules\Client\Command\ClientCompany\Update\UpdateClientCompanyCommand;
use App\Modules\Client\Command\ClientCompany\Update\UpdateClientCompanyHandler;
use App\Modules\Client\Entity\ClientCompany\ClientCompanyRepository;
use App\Modules\Client\ReadModel\ClientCompany\ClientCompanyItem;

final readonly class ClientCompanySyncerService
{
    public function __construct(
        private ClientCompanyRepository $repository,
        private CreateClientCompanyHandler $createHandler,
        private UpdateClientCompanyHandler $updateHandler,
        private DeleteClientCompanyHandler $deleteHandler,
    ) {}

    /**
     * @param list<ClientCompanyItem> $items
     */
    public function sync(int $clientId, array $items): void
    {
        $currentCompanies = $this->repository->findByClientId($clientId);
        $currentIds = array_map(static fn($c) => $c->id, $currentCompanies);
        $commandIds = array_filter(array_map(static fn($c) => $c->id, $items));

        foreach ($currentCompanies as $company) {
            if (!in_array($company->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteClientCompanyCommand($company->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateClientCompanyCommand(
                    id: $item->id,
                    companyName: $item->name
                ));
            } else {
                $this->createHandler->handle(new CreateClientCompanyCommand(
                    clientId: $clientId,
                    companyName: $item->name
                ));
            }
        }
    }
}
