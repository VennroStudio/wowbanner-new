<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Product;

use App\Components\Http\Response\JsonDataItemsResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Unifier\Product\ProductUnifier;
use App\Modules\Product\Query\Product\FindAll\ProductFindAllFetcher;
use App\Modules\Product\Query\Product\FindAll\ProductFindAllQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Get(
    path: '/products',
    description: 'Список продуктов с пагинацией и поиском по названию',
    summary: 'Список продуктов',
    security: [['bearerAuth' => []]],
    tags: ['Products'],
    parameters: [
        new OA\Parameter(name: 'page', description: 'Номер страницы', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        new OA\Parameter(name: 'perPage', description: 'Количество на странице', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
        new OA\Parameter(name: 'search', description: 'Поиск по названию', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Список продуктов'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class GetProductsAction implements RequestHandlerInterface
{
    public function __construct(
        private ProductFindAllFetcher $fetcher,
        private ProductUnifier        $unifier,
        private Denormalizer          $denormalizer,
        private Validator             $validator,
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
            ProductFindAllQuery::class,
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataItemsResponse(
            count: $result->count,
            items: $this->unifier->unify(null, $result->items),
        );
    }
}
