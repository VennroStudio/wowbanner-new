<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialImage\FindByMaterialIds;

final readonly class MaterialImageFindByMaterialIdsQuery
{
    /**
     * @param list<int> $materialIds
     */
    public function __construct(
        public array $materialIds,
    ) {}
}
