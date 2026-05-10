<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\Router\Route;
use App\Http\Unifier\Order\OrderUnifier;
use App\Modules\Order\Query\Order\GetById\OrderGetByIdFetcher;
use App\Modules\Order\Query\Order\GetById\OrderGetByIdQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/orders/{id}',
    description: 'Получение подробной информации о заказе',
    summary: 'Получить заказ по ID',
    security: [['bearerAuth' => []]],
    tags: ['Orders'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID заказа', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Данные заказа'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 404, description: 'Заказ не найден'),
    ]
)]
final readonly class GetOrderByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private OrderGetByIdFetcher $fetcher,
        private OrderUnifier $unifier,
    ) {}

    /**
     * @throws JsonException
     * @throws Exception
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = Route::getArgumentToInt($request, 'id');
        $item = $this->fetcher->fetch(new OrderGetByIdQuery($id));

        return new JsonDataResponse($this->unifier->unifyOne(null, $item));
    }
}
