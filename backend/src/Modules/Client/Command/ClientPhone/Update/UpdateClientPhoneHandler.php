<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientPhone\Update;

use App\Modules\Client\Entity\ClientPhone\ClientPhoneRepository;

final readonly class UpdateClientPhoneHandler
{
    public function __construct(
        private ClientPhoneRepository $repository,
    ) {}

    public function handle(UpdateClientPhoneCommand $command): void
    {
        $phone = $this->repository->getById($command->id);
        $phone->edit(
            type: $command->type,
            phone: $command->phone,
        );
    }
}
