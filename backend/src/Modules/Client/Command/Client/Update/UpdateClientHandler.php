<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\Client\Update;

use App\Components\Flusher\FlusherInterface;
use App\Modules\Client\Entity\Client\ClientRepository;
use App\Modules\Client\Entity\Client\Fields\ClientType;
use App\Modules\Client\Entity\Client\Fields\Docs;
use App\Modules\Client\Permission\ClientPermission;
use App\Modules\Client\Service\ClientCompanySyncerService;
use App\Modules\Client\Service\ClientPermissionService;
use App\Modules\Client\Service\ClientPhoneSyncerService;
use App\Modules\Client\Service\ClientValidatorService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateClientHandler
{
    public function __construct(
        private ClientRepository $repository,
        private FlusherInterface $flusher,
        private ClientPermissionService $permissionService,
        private ClientValidatorService $validator,
        private ClientPhoneSyncerService $phoneSyncer,
        private ClientCompanySyncerService $companySyncer,
    ) {}

    public function handle(UpdateClientCommand $command): void
    {
        $this->permissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: ClientPermission::UPDATE,
        );

        $client = $this->repository->getById($command->id);

        $phones = $this->validator->normalizePhones($command->phones);

        $this->validator->validate(
            email: $command->email,
            type: $command->type,
            phones: $phones,
            companies: $command->companies,
            clientId: $client->id,
        );

        $client->edit(
            lastName: $command->lastName,
            firstName: $command->firstName,
            middleName: $command->middleName,
            email: $command->email,
            docs: Docs::from($command->docs),
            type: ClientType::from($command->type),
            info: $command->info,
        );

        $this->phoneSyncer->sync($client->id, $phones);
        $this->companySyncer->sync($client->id, $command->companies);

        $this->flusher->flush();
    }
}
