<?php

declare(strict_types=1);

namespace App\Modules\Client\Service;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\Client\Entity\Client\Fields\ClientType;
use App\Modules\Client\Query\Client\Exists\ClientEmailExistsFetcher;
use App\Modules\Client\Query\Client\Exists\ClientEmailExistsQuery;
use App\Modules\Client\Query\ClientPhone\Exists\ClientPhoneExistsFetcher;
use App\Modules\Client\Query\ClientPhone\Exists\ClientPhoneExistsQuery;
use App\Modules\Client\ReadModel\ClientCompany\ClientCompanyItem;
use App\Modules\Client\ReadModel\ClientPhone\ClientPhoneItem;

final readonly class ClientValidatorService
{
    public function __construct(
        private ClientEmailExistsFetcher $emailFetcher,
        private ClientPhoneExistsFetcher $phoneFetcher,
    ) {}

    /**
     * @param list<ClientPhoneItem> $phones
     * @param list<ClientCompanyItem> $companies
     */
    public function validate(
        ?string $email,
        int $type,
        array $phones,
        array $companies,
        ?int $clientId = null
    ): void {
        $this->validateEmail($email, $clientId);
        $this->validatePhones($phones, $clientId);
        $this->validateLegalType($type, $companies);
    }

    private function validateEmail(?string $email, ?int $clientId): void
    {
        if ($email === null || $email === '') {
            return;
        }

        if ($this->emailFetcher->exists(new ClientEmailExistsQuery($email, $clientId))) {
            throw new DomainExceptionModule(
                module: 'client',
                message: 'error.email_already_taken',
                code: 4
            );
        }
    }

    /**
     * @param list<ClientPhoneItem> $phones
     */
    private function validatePhones(array $phones, ?int $clientId): void
    {
        foreach ($phones as $item) {
            if ($this->phoneFetcher->exists(new ClientPhoneExistsQuery($item->phone, $clientId))) {
                throw new DomainExceptionModule(
                    module: 'client',
                    message: 'error.phone_already_taken',
                    code: 5
                );
            }
        }
    }

    /**
     * @param list<ClientCompanyItem> $companies
     */
    private function validateLegalType(int $type, array $companies): void
    {
        if ($type === ClientType::LEGAL->value && count($companies) === 0) {
            throw new DomainExceptionModule(
                module: 'client',
                message: 'error.legal_client_must_have_company',
                code: 6
            );
        }
    }
}
