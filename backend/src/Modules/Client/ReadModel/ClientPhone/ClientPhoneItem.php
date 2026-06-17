<?php

declare(strict_types=1);

namespace App\Modules\Client\ReadModel\ClientPhone;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ClientPhoneItem
{
    private const int TYPE_MIN = 1;
    private const int TYPE_MAX = 2;

    public function __construct(
        #[Assert\Positive]
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.client_phone_type_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.client_phone_type_invalid',
            min: self::TYPE_MIN,
            max: self::TYPE_MAX
        )]
        public int $type,
        #[Assert\NotBlank(message: 'validation.phone_required')]
        public string $phone,
    ) {}
}
