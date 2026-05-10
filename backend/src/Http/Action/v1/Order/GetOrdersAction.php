<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Order;

use App\Components\Http\Response\JsonDataItemsResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Unifier\Order\OrderUnifier;
use App\Modules\Order\Query\Order\FindAll\OrderFindAllFetcher;
use App\Modules\Order\Query\Order\FindAll\OrderFindAllQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Get(
    path: '/orders',
    description: 'Получение списка заказов с пагинацией, поиском и фильтрами',
    summary: 'Список заказов',
    security: [['bearerAuth' => []]],
    tags: ['Orders'],
    parameters: [
        new OA\Parameter(name: 'page', description: 'Номер страницы', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        new OA\Parameter(name: 'perPage', description: 'Количество элементов на странице', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
        new OA\Parameter(name: 'search', description: 'Поиск по клиенту', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'dateFrom', description: 'Начало периода (YYYY-MM-DD)', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
        new OA\Parameter(name: 'dateTo', description: 'Конец периода (YYYY-MM-DD)', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
        new OA\Parameter(name: 'printId', description: 'Фильтр по типу печати', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'materialId', description: 'Фильтр по материалу', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'optionId', description: 'Фильтр по опции материала', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'docs', description: 'Фильтр по типу документа клиента', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'managerId', description: 'Фильтр по менеджеру', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'designerId', description: 'Фильтр по дизайнеру', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'statusType', description: 'Фильтр по статусу', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'storageType', description: 'Фильтр по складу', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'serviceType', description: 'Фильтр по типу услуги', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'archived', description: 'Показывать архивные заказы', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
        new OA\Parameter(name: 'deleted', description: 'Показывать удалённые заказы', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Список заказов'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
    ]
)]
final readonly class GetOrdersAction implements RequestHandlerInterface
{
    public function __construct(
        private OrderFindAllFetcher $fetcher,
        private OrderUnifier $unifier,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws Exception
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->denormalizer->denormalize(
            $request->getQueryParams(),
            OrderFindAllQuery::class,
        );

        $this->validator->validate($query);
        $result = $this->fetcher->fetch($query);

        return new JsonDataItemsResponse(
            count: $result->count,
            items: $this->unifier->unify(null, $result->items),
        );
    }
}
