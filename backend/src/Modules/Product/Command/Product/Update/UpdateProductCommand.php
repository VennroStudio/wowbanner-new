<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\Product\Update;

use App\Modules\Product\ReadModel\ProductMaterial\ProductMaterialItem;
use App\Modules\Product\ReadModel\ProductPrint\ProductPrintItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProductCommand
{
    private const int NAME_MIN_LENGTH = 2;
    private const int NAME_MAX_LENGTH = 255;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,

        #[Assert\NotBlank]
        public int $currentUserRole,

        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $id,

        #[Assert\NotBlank(message: 'validation.Product_name_required')]
        #[Assert\Length(
            min: self::NAME_MIN_LENGTH,
            max: self::NAME_MAX_LENGTH,
            minMessage: 'validation.Product_name_too_short',
            maxMessage: 'validation.Product_name_too_long',
        )]
        public string $name,
        /** @var list<ProductMaterialItem> */
        #[Assert\Valid]
        public array $materials = [],
        /** @var list<ProductPrintItem> */
        #[Assert\Valid]
        public array $prints = [],
    ) {}
}
