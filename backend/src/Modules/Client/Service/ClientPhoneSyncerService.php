<?php

declare(strict_types=1);

namespace App\Modules\Client\Service;

use App\Modules\Client\Command\ClientPhone\Create\CreateClientPhoneCommand;
use App\Modules\Client\Command\ClientPhone\Create\CreateClientPhoneHandler;
use App\Modules\Client\Command\ClientPhone\Delete\DeleteClientPhoneCommand;
use App\Modules\Client\Command\ClientPhone\Delete\DeleteClientPhoneHandler;
use App\Modules\Client\Command\ClientPhone\Update\UpdateClientPhoneCommand;
use App\Modules\Client\Command\ClientPhone\Update\UpdateClientPhoneHandler;
use App\Modules\Client\Entity\ClientPhone\ClientPhoneRepository;
use App\Modules\Client\Entity\ClientPhone\Fields\PhoneType;
use App\Modules\Client\ReadModel\ClientPhone\ClientPhoneItem;

final readonly class ClientPhoneSyncerService
{
    public function __construct(
        private ClientPhoneRepository $repository,
        private CreateClientPhoneHandler $createHandler,
        private UpdateClientPhoneHandler $updateHandler,
        private DeleteClientPhoneHandler $deleteHandler,
    ) {}

    /**
     * @param list<ClientPhoneItem> $items
     */
    public function sync(int $clientId, array $items): void
    {
        $currentPhones = $this->repository->findByClientId($clientId);
        $currentIds = array_map(static fn($p) => $p->id, $currentPhones);
        $commandIds = array_filter(array_map(static fn($p) => $p->id, $items));

        foreach ($currentPhones as $phone) {
            if (!in_array($phone->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteClientPhoneCommand($phone->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateClientPhoneCommand(
                    id: $item->id,
                    type: PhoneType::from($item->type),
                    phone: $item->phone
                ));
            } else {
                $this->createHandler->handle(new CreateClientPhoneCommand(
                    clientId: $clientId,
                    type: PhoneType::from($item->type),
                    phone: $item->phone
                ));
            }
        }
    }
}
