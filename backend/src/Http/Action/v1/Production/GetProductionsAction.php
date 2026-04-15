<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Production;

use App\Components\Http\Response\JsonDataItemsResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Unifier\Production\ProductionUnifier;
use App\Modules\Production\Query\Production\FindAll\ProductionFindAllFetcher;
use App\Modules\Production\Query\Production\FindAll\ProductionFindAllQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Get(
    path: '/productions',
    description: 'Список производств с пагинацией и поиском по названию',
    summary: 'Список производств',
    security: [['bearerAuth' => []]],
    tags: ['Productions'],
    parameters: [
        new OA\Parameter(name: 'page', description: 'Номер страницы', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        new OA\Parameter(name: 'perPage', description: 'Количество на странице', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
        new OA\Parameter(name: 'search', description: 'Поиск по названию', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Список производств'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class GetProductionsAction implements RequestHandlerInterface
{
    public function __construct(
        private ProductionFindAllFetcher $fetcher,
        private ProductionUnifier $unifier,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws Exception
     * @throws JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->denormalizer->denormalize(
            $request->getQueryParams(),
            ProductionFindAllQuery::class,
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataItemsResponse(
            count: $result->count,
            items: $this->unifier->unify(null, $result->items),
        );
    }
}
