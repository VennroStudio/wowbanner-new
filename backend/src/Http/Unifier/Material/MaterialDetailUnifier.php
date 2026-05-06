<?php

declare(strict_types=1);

namespace App\Http\Unifier\Material;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Material\Query\MaterialOption\FindByMaterialId\MaterialOptionFindByMaterialIdFetcher;
use App\Modules\Material\Query\MaterialOption\FindByMaterialId\MaterialOptionFindByMaterialIdQuery;
use App\Modules\Material\Query\MaterialPricingByArea\FindByMaterialIdAndOptionId\MaterialPricingByAreaFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingByArea\FindByMaterialIdAndOptionId\MaterialPricingByAreaFindByMaterialIdAndOptionIdQuery;
use App\Modules\Material\Query\MaterialPricingByPiece\FindByMaterialIdAndOptionId\MaterialPricingByPieceFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingByPiece\FindByMaterialIdAndOptionId\MaterialPricingByPieceFindByMaterialIdAndOptionIdQuery;
use App\Modules\Material\Query\MaterialPricingCut\FindByMaterialIdAndOptionId\MaterialPricingCutFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingCut\FindByMaterialIdAndOptionId\MaterialPricingCutFindByMaterialIdAndOptionIdQuery;
use App\Modules\Material\Query\MaterialProcessing\FindByMaterialIdAndOptionId\MaterialProcessingFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialProcessing\FindByMaterialIdAndOptionId\MaterialProcessingFindByMaterialIdAndOptionIdQuery;
use App\Modules\Material\ReadModel\Material\Interface\MaterialModelInterface;
use App\Modules\Material\ReadModel\MaterialOption\MaterialOptionByMaterialId;
use Doctrine\DBAL\Exception;
use Override;

final readonly class MaterialDetailUnifier implements UnifierInterface
{
    public function __construct(
        private MaterialUnifier $materialUnifier,
        private MaterialOptionFindByMaterialIdFetcher $materialOptionFetcher,
        private MaterialPricingByAreaFindByMaterialIdAndOptionIdFetcher $materialPricingByAreaFetcher,
        private MaterialPricingByPieceFindByMaterialIdAndOptionIdFetcher $materialPricingByPieceFetcher,
        private MaterialPricingCutFindByMaterialIdAndOptionIdFetcher $materialPricingCutFetcher,
        private MaterialProcessingFindByMaterialIdAndOptionIdFetcher $materialProcessingFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof MaterialModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
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
            fn(MaterialModelInterface $item): array => $this->map($item),
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
        /** @var MaterialModelInterface $item */
        $data = $this->materialUnifier->unifyOne(null, $item);
        $data['options'] = $this->buildOptions($item->getId());

        return $data;
    }

    /**
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    private function buildOptions(int $materialId): array
    {
        $options = $this->materialOptionFetcher->fetch(new MaterialOptionFindByMaterialIdQuery($materialId));

        return array_map(
            fn(MaterialOptionByMaterialId $option): array => $this->mapOption($materialId, $option),
            $options,
        );
    }

    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    private function mapOption(int $materialId, MaterialOptionByMaterialId $option): array
    {
        $pricingByArea = array_map(
            static fn(object $item): array => [
                'id' => $item->getId(),
                'dpiType' => $item->toArray()['dpi_type'],
                'areaRangeType' => $item->toArray()['area_range_type'],
                'price' => $item->toArray()['price'],
                'cost' => $item->toArray()['cost'],
                'printHours' => $item->toArray()['print_hours'],
            ],
            $this->materialPricingByAreaFetcher->fetch(
                new MaterialPricingByAreaFindByMaterialIdAndOptionIdQuery($materialId, $option->getId())
            ),
        );

        $pricingByPiece = array_map(
            static fn(object $item): array => [
                'id' => $item->getId(),
                'variantType' => $item->toArray()['variant_type'],
                'price' => $item->toArray()['price'],
                'cost' => $item->toArray()['cost'],
                'printHours' => $item->toArray()['print_hours'],
            ],
            $this->materialPricingByPieceFetcher->fetch(
                new MaterialPricingByPieceFindByMaterialIdAndOptionIdQuery($materialId, $option->getId())
            ),
        );

        $pricingByCut = array_map(
            static fn(object $item): array => [
                'id' => $item->getId(),
                'type' => $item->toArray()['type'],
                'price' => $item->toArray()['price'],
            ],
            $this->materialPricingCutFetcher->fetch(
                new MaterialPricingCutFindByMaterialIdAndOptionIdQuery($materialId, $option->getId())
            ),
        );

        $processings = array_map(
            static fn(object $item): array => [
                'id' => $item->getId(),
                'processingId' => $item->getProcessingId(),
            ],
            $this->materialProcessingFetcher->fetch(
                new MaterialProcessingFindByMaterialIdAndOptionIdQuery($materialId, $option->getId())
            ),
        );

        return [
            'id' => $option->getId(),
            'name' => $option->name,
            'pricingType' => [
                'id' => $option->pricingType->value,
                'label' => $option->pricingType->getLabel(),
            ],
            'isCut' => $option->isCut,
            'pricingByArea' => $pricingByArea,
            'pricingByPiece' => $pricingByPiece,
            'pricingByCut' => $pricingByCut,
            'processings' => $processings,
        ];
    }
}
