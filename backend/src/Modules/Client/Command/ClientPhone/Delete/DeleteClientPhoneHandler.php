<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\ClientPhone\Delete;

use App\Modules\Client\Entity\ClientPhone\ClientPhoneRepository;

final readonly class DeleteClientPhoneHandler
{
    public function __construct(
        private ClientPhoneRepository $repository,
    ) {}

    public function handle(DeleteClientPhoneCommand $command): void
    {
        $phone = $this->repository->getById($command->id);
        $this->repository->remove($phone);
    }
}
