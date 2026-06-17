<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientCompany;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ClientCompanyItem
{
    public function __construct(
        #[Assert\Positive]
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.company_required')]
        public string $name,
    ) {}
}
