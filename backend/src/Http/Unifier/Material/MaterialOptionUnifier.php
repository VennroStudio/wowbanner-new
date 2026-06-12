<?php

declare(strict_types=1);

namespace App\Http\Unifier\Material;

use App\Components\Http\Unifier\UnifierInterface;
use App\Components\ReadModel\ReadModelInterface;
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

    /**
     * @throws Exception
     */
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

        $data['pricing_by_area'] = array_map(
            static fn(ReadModelInterface $price): array => $price->toArray(),
            $this->materialPricingByAreaFetcher->fetch(
                new MaterialPricingByAreaFindByMaterialIdAndOptionIdQuery($materialId, $optionId),
            ),
        );
        $data['pricing_by_piece'] = array_map(
            static fn(ReadModelInterface $price): array => $price->toArray(),
            $this->materialPricingByPieceFetcher->fetch(
                new MaterialPricingByPieceFindByMaterialIdAndOptionIdQuery($materialId, $optionId),
            ),
        );
        $data['pricing_by_cut'] = array_map(
            static fn(ReadModelInterface $price): array => $price->toArray(),
            $this->materialPricingCutFetcher->fetch(
                new MaterialPricingCutFindByMaterialIdAndOptionIdQuery($materialId, $optionId),
            ),
        );
        $data['processings'] = array_map(
            static fn(ReadModelInterface $processing): array => $processing->toArray(),
            $this->materialProcessingFetcher->fetch(
                new MaterialProcessingFindByMaterialIdAndOptionIdQuery($materialId, $optionId),
            ),
        );

        return $data;
    }
}
