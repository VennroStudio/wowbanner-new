<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientPhone;

interface ClientPhoneRepository
{
    public function getById(int $id): ClientPhone;
    public function findById(int $id): ?ClientPhone;

    /** @return list<ClientPhone> */
    public function findByClientId(int $clientId): array;

    public function add(ClientPhone $phone): void;
    public function remove(ClientPhone $phone): void;
}
