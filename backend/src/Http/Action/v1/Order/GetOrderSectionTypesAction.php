<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Enum\EnumModel;
use App\Components\Http\Response\JsonDataResponse;
use App\Modules\Order\Entity\OrderSection\Fields\Enums\SectionType;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/orders/section-types',
    description: 'Получение списка секций хранения заказа',
    summary: 'Список секций хранения',
    security: [['bearerAuth' => []]],
    tags: ['Orders'],
    responses: [
        new OA\Response(response: 200, description: 'Успешный ответ со списком'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetOrderSectionTypesAction implements RequestHandlerInterface
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(EnumModel::fromEnumClass(SectionType::class));
    }
}
