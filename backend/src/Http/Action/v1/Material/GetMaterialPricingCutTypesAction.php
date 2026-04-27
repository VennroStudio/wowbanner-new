<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Enum\EnumModel;
use App\Components\Http\Response\JsonDataResponse;
use App\Modules\Material\Entity\MaterialPricingCut\Fields\Enums\MaterialPricingCutType;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/materials/pricing-cut-types',
    description: 'Справочник типа реза для ценообразования реза материала',
    summary: 'Типы реза',
    security: [['bearerAuth' => []]],
    tags: ['Materials'],
    responses: [
        new OA\Response(response: 200, description: 'Список значений { id, label }'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetMaterialPricingCutTypesAction implements RequestHandlerInterface
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(EnumModel::fromEnumClass(MaterialPricingCutType::class));
    }
}
