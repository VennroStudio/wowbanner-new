<?php

declare(strict_types=1);

namespace App\Http\Unifier\Product;

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Product\Query\ProductMaterial\FindByProductIds\ProductMaterialFindByProductIdsFetcher;
use App\Modules\Product\Query\ProductMaterial\FindByProductIds\ProductMaterialFindByProductIdsQuery;
use App\Modules\Product\Query\ProductPrint\FindByProductIds\ProductPrintFindByProductIdsFetcher;
use App\Modules\Product\Query\ProductPrint\FindByProductIds\ProductPrintFindByProductIdsQuery;
use App\Modules\Product\ReadModel\Product\Interface\ProductModelInterface;
use App\Modules\Product\ReadModel\ProductMaterial\ProductMaterialByProductId;
use App\Modules\Product\ReadModel\ProductPrint\ProductPrintByProductId;
use Doctrine\DBAL\Exception;
use Override;

final readonly class ProductUnifier implements UnifierInterface
{
    public function __construct(
        private ProductMaterialFindByProductIdsFetcher $materialFetcher,
        private ProductPrintFindByProductIdsFetcher    $printFetcher,
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
     * @param list<ProductModelInterface> $items
     * @return list<array<string, mixed>>
     * @throws Exception
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $ids = array_map(static fn(ProductModelInterface $i): int => $i->getId(), $items);

        $materialsByProduct = $this->groupMaterials(
            $this->materialFetcher->fetch(new ProductMaterialFindByProductIdsQuery($ids)),
        );
        $printsByProduct = $this->groupPrints(
            $this->printFetcher->fetch(new ProductPrintFindByProductIdsQuery($ids)),
        );

        return array_map(
            fn(ProductModelInterface $item): array => $this->map($item, $materialsByProduct, $printsByProduct),
            $items,
        );
    }

    /**
     * @param array<int, list<array<string, mixed>>> $materialsByProduct
     * @param array<int, list<array<string, mixed>>> $printsByProduct
     */
    #[Override]
    public function map(object $item, array $materialsByProduct = [], array $printsByProduct = []): array
    {
        /** @var ProductModelInterface $item */
        $data = $item->toArray();
        $data['materials'] = $materialsByProduct[$item->getId()] ?? [];
        $data['prints'] = $printsByProduct[$item->getId()] ?? [];

        return UnifierHelper::withTimestamps($data, $item);
    }

    /**
     * @param list<ProductMaterialByProductId> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupMaterials(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->productId][] = UnifierHelper::toArrayWithout($item, 'product_id');
        }

        return $grouped;
    }

    /**
     * @param list<ProductPrintByProductId> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupPrints(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->productId][] = UnifierHelper::toArrayWithout($item, 'product_id');
        }

        return $grouped;
    }
}
