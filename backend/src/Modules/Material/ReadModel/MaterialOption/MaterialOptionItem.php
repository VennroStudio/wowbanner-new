<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialOption;

use App\Modules\Material\ReadModel\MaterialPricingByArea\MaterialPricingByAreaItem;
use App\Modules\Material\ReadModel\MaterialPricingByPiece\MaterialPricingByPieceItem;
use App\Modules\Material\ReadModel\MaterialPricingCut\MaterialPricingCutItem;
use App\Modules\Material\ReadModel\MaterialProcessing\MaterialProcessingLinkItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class MaterialOptionItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.material_option_name_required')]
        #[Assert\Length(
            min: 1,
            max: 255,
            maxMessage: 'validation.material_option_name_too_long',
        )]
        public string $name,
        #[Assert\NotBlank(message: 'validation.material_option_pricing_type_required')]
        #[Assert\Range(
            notInRangeMessage: 'validation.material_option_pricing_type_invalid',
            min: 1,
            max: 2
        )]
        public int $pricingType,
        public bool $isCut = false,

        /** @var list<MaterialPricingByAreaItem> */
        #[Assert\Valid]
        public array $pricingByArea = [],

        /** @var list<MaterialPricingByPieceItem> */
        #[Assert\Valid]
        public array $pricingByPiece = [],

        /** @var list<MaterialPricingCutItem> */
        #[Assert\Valid]
        public array $pricingByCut = [],

        /** @var list<MaterialProcessingLinkItem> */
        #[Assert\Valid]
        public array $processings = [],
    ) {}
}
