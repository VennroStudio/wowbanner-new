<?php

declare(strict_types=1);

namespace App\Http\Unifier\Material;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Material\Query\MaterialPricingByArea\FindByMaterialIdAndOptionId\MaterialPricingByAreaFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingByArea\FindByMaterialIdAndOptionId\MaterialPricingByAreaFindByMaterialIdAndOptionIdQuery;
use App\Modules\Material\Query\MaterialPricingByPiece\FindByMaterialIdAndOptionId\MaterialPricingByPieceFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingByPiece\FindByMaterialIdAndOptionId\MaterialPricingByPieceFindByMaterialIdAndOptionIdQuery;
use App\Modules\Material\Query\MaterialPricingCut\FindByMaterialIdAndOptionId\MaterialPricingCutFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingCut\FindByMaterialIdAndOptionId\MaterialPricingCutFindByMaterialIdAndOptionIdQuery;
use App\Modules\Material\Query\MaterialProcessing\FindByMaterialIdAndOptionId\MaterialProcessingFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialProcessing\FindByMaterialIdAndOptionId\MaterialProcessingFindByMaterialIdAndOptionIdQuery;
use App\Modules\Material\ReadModel\MaterialOption\Interface\MaterialOptionModelInterface;
use Doctrine\DBAL\Exception;
use Override;

final readonly class MaterialOptionUnifier implements UnifierInterface
{
    public function __construct(
        private MaterialPricingByAreaFindByMaterialIdAndOptionIdFetcher $materialPricingByAreaFetcher,
        private MaterialPricingByPieceFindByMaterialIdAndOptionIdFetcher $materialPricingByPieceFetcher,
        private MaterialPricingCutFindByMaterialIdAndOptionIdFetcher $materialPricingCutFetcher,
        private MaterialProcessingFindByMaterialIdAndOptionIdFetcher $materialProcessingFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof MaterialOptionModelInterface) {
            return [];
        }

        return $this->map($item);
    }

    /**
     * @param list<object> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        return array_map(
            fn(MaterialOptionModelInterface $item): array => $this->map($item),
            $items,
        );
    }

    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    #[Override]
    public function map(object $item): array
    {
        /** @var MaterialOptionModelInterface $item */
        $data = $item->toArray();
        $materialId = $item->getMaterialId();
        $optionId = $item->getId();

        $data['pricingByArea'] = array_map(
            static fn(object $price): array => UnifierHelper::toArrayWithout($price, 'material_id', 'option_id'),
            $this->materialPricingByAreaFetcher->fetch(
                new MaterialPricingByAreaFindByMaterialIdAndOptionIdQuery($materialId, $optionId),
            ),
        );
        $data['pricingByPiece'] = array_map(
            static fn(object $price): array => UnifierHelper::toArrayWithout($price, 'material_id', 'option_id'),
            $this->materialPricingByPieceFetcher->fetch(
                new MaterialPricingByPieceFindByMaterialIdAndOptionIdQuery($materialId, $optionId),
            ),
        );
        $data['pricingByCut'] = array_map(
            static fn(object $price): array => UnifierHelper::toArrayWithout($price, 'material_id', 'option_id'),
            $this->materialPricingCutFetcher->fetch(
                new MaterialPricingCutFindByMaterialIdAndOptionIdQuery($materialId, $optionId),
            ),
        );
        $data['processings'] = array_map(
            static fn(object $processing): array => UnifierHelper::toArrayWithout($processing, 'material_id', 'option_id'),
            $this->materialProcessingFetcher->fetch(
                new MaterialProcessingFindByMaterialIdAndOptionIdQuery($materialId, $optionId),
            ),
        );

        return $data;
    }
}
