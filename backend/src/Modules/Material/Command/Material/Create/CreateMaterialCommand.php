<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\Material\Create;

use App\Modules\Material\ReadModel\MaterialImage\MaterialImageItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateMaterialCommand
{
    private const int NAME_MIN_LENGTH = 2;
    private const int NAME_MAX_LENGTH = 255;
    private const int DESCRIPTION_MAX_LENGTH = 65535;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
        #[Assert\NotBlank(message: 'validation.material_name_required')]
        #[Assert\Length(
            min: self::NAME_MIN_LENGTH,
            max: self::NAME_MAX_LENGTH,
            minMessage: 'validation.material_name_too_short',
            maxMessage: 'validation.material_name_too_long',
        )]
        public string $name,
        #[Assert\Length(max: self::DESCRIPTION_MAX_LENGTH, maxMessage: 'validation.material_description_too_long')]
        public string $description = '',
        /** @var MaterialImageItem[] */
        #[Assert\All([new Assert\Type(MaterialImageItem::class)])]
        public array $images = [],
    ) {}
}

