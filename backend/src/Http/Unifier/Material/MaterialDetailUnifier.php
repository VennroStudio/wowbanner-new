<?php

declare(strict_types=1);

namespace App\Http\Unifier\Material;

use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\Material\Query\MaterialOption\FindByMaterialId\MaterialOptionFindByMaterialIdFetcher;
use App\Modules\Material\Query\MaterialOption\FindByMaterialId\MaterialOptionFindByMaterialIdQuery;
use App\Modules\Material\ReadModel\Material\Interface\MaterialModelInterface;
use Doctrine\DBAL\Exception;
use Override;

final readonly class MaterialDetailUnifier implements UnifierInterface
{
    public function __construct(
        private MaterialUnifier $materialUnifier,
        private MaterialOptionFindByMaterialIdFetcher $materialOptionFetcher,
        private MaterialOptionUnifier $materialOptionUnifier,
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
            fn(object $option): array => $this->materialOptionUnifier->map($option),
            $options,
        );
    }
}
