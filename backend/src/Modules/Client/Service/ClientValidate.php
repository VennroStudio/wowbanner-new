<?php

declare(strict_types=1);

namespace App\Modules\Client\Service;

final readonly class ClientValidate
{
    public function __construct(
        private ClientRepository $clientRepository,
    ) {
    }

    public function validate(): void
    {

    }

    /**
     * @param list<string> $errors
     */
    private function validateEmail(?string $email, array &$errors, ?int $clientId = null): void
    {
        if ($email === null || $email === '') {
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный формат email';
            return;
        }

        $existingClient = $this->clientRepository->findByEmail($email);
        if ($existingClient !== null && $existingClient->getId() !== $clientId) {
            $errors[] = 'Клиент с таким email уже существует';
        }
    }

    /**
     * @param list<string> $errors
     */
    private function validatePhone(?string $phone, array &$errors, ?int $clientId = null): void
    {
        if ($phone === null || $phone === '') {
            return;
        }

        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
        if ($cleanPhone === null || strlen($cleanPhone) < 10) {
            $errors[] = 'Некорректный формат телефона';
            return;
        }

        $existingClient = $this->clientRepository->findByPhone($phone);
        if ($existingClient !== null && $existingClient->getId() !== $clientId) {
            $errors[] = 'Клиент с таким телефоном уже существует';
        }
    }

    /**
     * @param list<string> $companies
     * @param list<string> $errors
     */
    private function validateLegalCompanies(?ClientType $type, array $companies, array &$errors): void
    {
        if ($type === ClientType::LEGAL && $companies === []) {
            $errors[] = 'Для юридического лица необходимо указать хотя бы одну компанию';
        }
    }
}
