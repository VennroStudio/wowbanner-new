<?php

declare(strict_types=1);

namespace App\Modules\User\Command\User\Create;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateUserCommand
{
    private const int NAME_MIN_LENGTH = 2;
    private const int NAME_MAX_LENGTH = 60;
    private const int EMAIL_MAX_LENGTH = 255;
    private const string NAME_PATTERN = "/^\\p{L}[\\p{L}\\s'\\-]*$/u";

    public function __construct(
        #[Assert\NotBlank(message: 'validation.last_name_required')]
        #[Assert\Length(
            min: self::NAME_MIN_LENGTH,
            max: self::NAME_MAX_LENGTH,
            minMessage: 'validation.last_name_too_short',
            maxMessage: 'validation.last_name_too_long',
        )]
        #[Assert\Regex(pattern: self::NAME_PATTERN, message: 'validation.last_name_invalid')]
        public string $lastName,
        #[Assert\NotBlank(message: 'validation.first_name_required')]
        #[Assert\Length(
            min: self::NAME_MIN_LENGTH,
            max: self::NAME_MAX_LENGTH,
            minMessage: 'validation.first_name_too_short',
            maxMessage: 'validation.first_name_too_long',
        )]
        #[Assert\Regex(pattern: self::NAME_PATTERN, message: 'validation.first_name_invalid')]
        public string $firstName,
        #[Assert\NotBlank(message: 'validation.email_required')]
        #[Assert\Email(message: 'validation.email_invalid')]
        #[Assert\Length(max: self::EMAIL_MAX_LENGTH, maxMessage: 'validation.email_too_long')]
        public string $email,
        public string $locale = 'en',
    ) {}
}
