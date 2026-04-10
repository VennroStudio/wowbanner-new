<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientCompany;

interface ClientCompanyRepository
{
    public function getById(int $id): ClientCompany;

    public function findById(int $id): ?ClientCompany;

    /** @return list<ClientCompany> */
    public function findByClientId(int $clientId): array;

    public function add(ClientCompany $company): void;

    public function remove(ClientCompany $company): void;
}
