<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientPhone\Create;

use App\Modules\Client\Entity\ClientPhone\ClientPhone;
use App\Modules\Client\Entity\ClientPhone\ClientPhoneRepository;

final readonly class CreateClientPhoneHandler
{
    public function __construct(
        private ClientPhoneRepository $repository,
    ) {}

    public function handle(CreateClientPhoneCommand $command): void
    {
        $phone = ClientPhone::create(
            clientId: $command->clientId,
            type: $command->type,
            phone: $command->phone,
        );
        $this->repository->add($phone);
    }
}
