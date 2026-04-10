<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientPhone;

interface ClientPhoneRepository
{
    public function add(ClientPhone $phone): void;
    public function remove(ClientPhone $phone): void;
}
