<?php

declare(strict_types=1);

namespace App\Modules\Client\Command\Client\Update;

use App\Modules\Client\ReadModel\ClientCompany\ClientCompanyItem;
use App\Modules\Client\ReadModel\ClientPhone\ClientPhoneItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateClientCommand
{
    private const int DOCS_MIN = 1;
    private const int DOCS_MAX = 3;
    private const int TYPE_MIN = 1;
    private const int TYPE_MAX = 2;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $id,
        #[Assert\NotBlank(message: 'validation.last_name_required')]
        public string $lastName,
        #[Assert\NotBlank(message: 'validation.first_name_required')]
        public string $firstName,
        #[Assert\NotBlank(message: 'validation.client_docs_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.client_docs_invalid',
            min: self::DOCS_MIN,
            max: self::DOCS_MAX
        )]
        public int $docs,
        #[Assert\NotBlank(message: 'validation.client_type_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.client_type_invalid',
            min: self::TYPE_MIN,
            max: self::TYPE_MAX
        )]
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
