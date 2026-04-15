<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\Production\Create;

use App\Modules\Production\ReadModel\ProductionMaterial\ProductionMaterialItem;
use App\Modules\Production\ReadModel\ProductionPrint\ProductionPrintItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateProductionCommand
{
    private const int NAME_MIN_LENGTH = 2;
    private const int NAME_MAX_LENGTH = 255;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,
        #[Assert\NotBlank]
        public int $currentUserRole,
        #[Assert\NotBlank(message: 'validation.production_name_required')]
        #[Assert\Length(
            min: self::NAME_MIN_LENGTH,
            max: self::NAME_MAX_LENGTH,
            minMessage: 'validation.production_name_too_short',
            maxMessage: 'validation.production_name_too_long',
        )]
        public string $name,
        /** @var list<ProductionMaterialItem> */
        #[Assert\Valid]
        public array $materials = [],
        /** @var list<ProductionPrintItem> */
        #[Assert\Valid]
        public array $prints = [],
    ) {}
}
