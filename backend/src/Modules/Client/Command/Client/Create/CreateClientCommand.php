<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\Client\Create;

use App\Modules\Client\ReadModel\ClientCompany\ClientCompanyItem;
use App\Modules\Client\ReadModel\ClientPhone\ClientPhoneItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateClientCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,

        #[Assert\NotBlank]
        public int $currentUserRole,

        #[Assert\NotBlank(message: 'validation.last_name_required')]
        public string $lastName,

        #[Assert\NotBlank(message: 'validation.first_name_required')]
        public string $firstName,

        #[Assert\NotBlank]
        public int $docs,

        #[Assert\NotBlank]
        public int $type,

        public ?string $middleName = null,

        #[Assert\Email(message: 'validation.email_invalid')]
        public ?string $email = null,

        public ?string $info = null,

        /** @var list<ClientPhoneItem> */
        #[Assert\Valid]
        public array $phones = [],

        /** @var list<ClientCompanyItem> */
        #[Assert\Valid]
        public array $companies = [],
    ) {}
}
