<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Enum\EnumModel;
use App\Components\Http\Response\JsonDataResponse;
use App\Modules\Order\Entity\OrderPayment\Fields\Enums\OperationType;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/orders/payment-operation-types',
    description: 'Получение списка типов платежной операции',
    summary: 'Список типов платежной операции',
    security: [['bearerAuth' => []]],
    tags: ['Orders'],
    responses: [
        new OA\Response(response: 200, description: 'Успешный ответ со списком'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetOrderPaymentOperationTypesAction implements RequestHandlerInterface
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(EnumModel::fromEnumClass(OperationType::class));
    }
}
