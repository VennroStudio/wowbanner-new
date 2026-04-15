<?php

declare(strict_types=1);

namespace App\Http\Unifier\Production;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Production\Query\ProductionMaterial\FindByProductionIds\ProductionMaterialFindByProductionIdsFetcher;
use App\Modules\Production\Query\ProductionMaterial\FindByProductionIds\ProductionMaterialFindByProductionIdsQuery;
use App\Modules\Production\Query\ProductionPrint\FindByProductionIds\ProductionPrintFindByProductionIdsFetcher;
use App\Modules\Production\Query\ProductionPrint\FindByProductionIds\ProductionPrintFindByProductionIdsQuery;
use App\Modules\Production\ReadModel\Production\Interface\ProductionModelInterface;
use App\Modules\Production\ReadModel\ProductionMaterial\ProductionMaterialByProductionId;
use App\Modules\Production\ReadModel\ProductionPrint\ProductionPrintByProductionId;
use Doctrine\DBAL\Exception;
use Override;

final readonly class ProductionUnifier implements UnifierInterface
{
    public function __construct(
        private ProductionMaterialFindByProductionIdsFetcher $materialFetcher,
        private ProductionPrintFindByProductionIdsFetcher $printFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if ($item === null) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<ProductionModelInterface> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $ids = array_map(static fn(ProductionModelInterface $i): int => $i->getId(), $items);

        $materialsByProduction = $this->groupMaterials(
            $this->materialFetcher->fetch(new ProductionMaterialFindByProductionIdsQuery($ids)),
        );
        $printsByProduction = $this->groupPrints(
            $this->printFetcher->fetch(new ProductionPrintFindByProductionIdsQuery($ids)),
        );

        return array_map(
            fn(ProductionModelInterface $item): array => $this->map($item, $materialsByProduction, $printsByProduction),
            $items,
        );
    }

    /**
     * @param array<int, list<array<string, mixed>>> $materialsByProduction
     * @param array<int, list<array<string, mixed>>> $printsByProduction
     */
    #[Override]
    public function map(object $item, array $materialsByProduction = [], array $printsByProduction = []): array
    {
        /** @var ProductionModelInterface $item */
        $data = $item->toArray();
        $data['materials'] = $materialsByProduction[$item->getId()] ?? [];
        $data['prints'] = $printsByProduction[$item->getId()] ?? [];

        return UnifierHelper::withTimestamps($data, $item);
    }

    /**
     * @param list<ProductionMaterialByProductionId> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupMaterials(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->productionId][] = UnifierHelper::toArrayWithout($item, 'production_id');
        }

        return $grouped;
    }

    /**
     * @param list<ProductionPrintByProductionId> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupPrints(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->productionId][] = UnifierHelper::toArrayWithout($item, 'production_id');
        }

        return $grouped;
    }
}
