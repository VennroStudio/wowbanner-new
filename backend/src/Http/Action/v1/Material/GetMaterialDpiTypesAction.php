<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Enum\EnumModel;
use App\Components\Http\Response\JsonDataResponse;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/materials/dpi-types',
    description: 'Справочник значений DPI для ценообразования материала по площади',
    summary: 'Типы DPI (по площади)',
    security: [['bearerAuth' => []]],
    tags: ['Materials'],
    responses: [
        new OA\Response(response: 200, description: 'Список значений { id, label }'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetMaterialDpiTypesAction implements RequestHandlerInterface
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(EnumModel::fromEnumClass(DpiType::class));
    }
}
