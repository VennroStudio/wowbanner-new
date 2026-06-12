<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Components\Fetcher\FetcherCache;
use App\Components\Fetcher\FetcherCacheKey;
use App\Modules\Material\Query\Material\GetById\MaterialGetByIdFetcher;
use App\Modules\Material\Query\MaterialOption\FindByMaterialId\MaterialOptionFindByMaterialIdFetcher;
use App\Modules\Material\Query\MaterialOption\GetById\MaterialOptionGetByIdFetcher;
use App\Modules\Material\Query\MaterialOption\GetByMaterialIdAndOptionId\MaterialOptionGetByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingByArea\FindByMaterialIdAndOptionId\MaterialPricingByAreaFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingByPiece\FindByMaterialIdAndOptionId\MaterialPricingByPieceFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialPricingCut\FindByMaterialIdAndOptionId\MaterialPricingCutFindByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialProcessing\FindByMaterialIdAndOptionId\MaterialProcessingFindByMaterialIdAndOptionIdFetcher;

final readonly class MaterialQueryCacheInvalidator
{
    public function __construct(
        private FetcherCache $fetcherCache,
    ) {}

    public function invalidateMaterialOption(int $id, int $materialId): void
    {
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(MaterialOptionGetByIdFetcher::CACHE_TAG, [$id])
        );
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(MaterialOptionFindByMaterialIdFetcher::CACHE_TAG, [$materialId])
        );
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(
                MaterialOptionGetByMaterialIdAndOptionIdFetcher::CACHE_TAG,
                [$materialId, $id]
            )
        );
    }

    public function invalidateByMaterialId(int $materialId): void
    {
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(MaterialGetByIdFetcher::CACHE_TAG, [$materialId])
        );
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(MaterialOptionFindByMaterialIdFetcher::CACHE_TAG, [$materialId])
        );
    }

    public function invalidateMaterialAndOptionContext(int $materialId, int $optionId): void
    {
        $parts = [$materialId, $optionId];

        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(MaterialProcessingFindByMaterialIdAndOptionIdFetcher::CACHE_TAG, $parts)
        );
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(MaterialPricingByAreaFindByMaterialIdAndOptionIdFetcher::CACHE_TAG, $parts)
        );
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(MaterialPricingByPieceFindByMaterialIdAndOptionIdFetcher::CACHE_TAG, $parts)
        );
        $this->fetcherCache->invalidateTag(
            FetcherCacheKey::tag(MaterialPricingCutFindByMaterialIdAndOptionIdFetcher::CACHE_TAG, $parts)
        );
    }
}
