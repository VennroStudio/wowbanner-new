<?php

declare(strict_types=1);

namespace App\Http\Unifier\Product;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Product\Query\ProductMaterial\FindByProductIds\ProductMaterialFindByProductIdsFetcher;
use App\Modules\Product\Query\ProductMaterial\FindByProductIds\ProductMaterialFindByProductIdsQuery;
use App\Modules\Product\Query\ProductPrint\FindByProductIds\ProductPrintFindByProductIdsFetcher;
use App\Modules\Product\Query\ProductPrint\FindByProductIds\ProductPrintFindByProductIdsQuery;
use App\Modules\Product\ReadModel\Product\Interface\ProductModelInterface;
use App\Modules\Product\ReadModel\ProductMaterial\Interface\ProductMaterialModelInterface;
use App\Modules\Product\ReadModel\ProductPrint\Interface\ProductPrintModelInterface;
use Doctrine\DBAL\Exception;
use Override;

final readonly class ProductUnifier implements UnifierInterface
{
    public function __construct(
        private ProductMaterialFindByProductIdsFetcher $materialFetcher,
        private ProductPrintFindByProductIdsFetcher $printFetcher,
        private ProductMaterialUnifier $materialUnifier,
        private ProductPrintUnifier $printUnifier,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof ProductModelInterface) {
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

        $ids = array_map(static fn (ProductModelInterface $i): int => $i->getId(), $items);

        $materialsByProduct = $this->groupMaterialsByProductId(
            $this->materialFetcher->fetch(new ProductMaterialFindByProductIdsQuery($ids)),
        );
        $printsByProduct = $this->groupPrintsByProductId(
            $this->printFetcher->fetch(new ProductPrintFindByProductIdsQuery($ids)),
        );

        return array_map(
            fn (ProductModelInterface $item): array => $this->map($item, $materialsByProduct, $printsByProduct),
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

        return $data;
    }

    /**
     * @param list<ProductMaterialModelInterface> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupMaterialsByProductId(array $items): array
    {
        $grouped = [];

        foreach ($items as $item) {
            $grouped[$item->getProductId()][] = $item;
        }

        $result = [];
        foreach ($grouped as $productId => $group) {
            $result[$productId] = $this->materialUnifier->unify(null, $group);
        }

        return $result;
    }

    /**
     * @param list<ProductPrintModelInterface> $items
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupPrintsByProductId(array $items): array
    {
        $grouped = [];

        foreach ($items as $item) {
            $grouped[$item->getProductId()][] = $item;
        }

        $result = [];
        foreach ($grouped as $productId => $group) {
            $result[$productId] = $this->printUnifier->unify(null, $group);
        }

        return $result;
    }
}
