<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\Client;

interface ClientRepository
{
    public function getById(int $id): Client;

    public function findById(int $id): ?Client;

    public function findByEmail(string $email): ?Client;

    public function findByPhone(string $phone): ?Client;

    public function add(Client $client): void;

    public function remove(Client $client): void;
}
