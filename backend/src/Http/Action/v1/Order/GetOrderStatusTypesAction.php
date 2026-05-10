<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Enum\EnumModel;
use App\Components\Http\Middleware\Identity\RequestIdentity;
use App\Components\Http\Response\JsonDataResponse;
use App\Modules\Order\Entity\Order\Fields\Enums\StatusType;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/orders/status-types',
    description: 'Получение списка статусов заказа, доступных текущей роли',
    summary: 'Список статусов заказа',
    security: [['bearerAuth' => []]],
    tags: ['Orders'],
    responses: [
        new OA\Response(response: 200, description: 'Успешный ответ со списком'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetOrderStatusTypesAction implements RequestHandlerInterface
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = RequestIdentity::get($request);

        return new JsonDataResponse(
            EnumModel::fromEnumClassForRole(StatusType::class, $identity->role),
        );
    }
}
