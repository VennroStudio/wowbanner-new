<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Product;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\ReadModel\ReadModelArray;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Product\Query\Product\GetBySelect\ProductGetBySelectFetcher;
use App\Modules\Product\Query\Product\GetBySelect\ProductGetBySelectQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Get(
    path: '/products/select',
    description: 'Получение списка продуктов для селекта с необязательным фильтром по типу печати',
    summary: 'Получить продукты для селекта',
    security: [['bearerAuth' => []]],
    tags: ['Products'],
    parameters: [
        new OA\Parameter(
            name: 'printId',
            description: 'Фильтр по типу печати',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetProductSelectAction implements RequestHandlerInterface
{
    public function __construct(
        private ProductGetBySelectFetcher $fetcher,
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
        /** @var ProductGetBySelectQuery $query */
        $query = $this->denormalizer->denormalize(
            $request->getQueryParams(),
            ProductGetBySelectQuery::class,
        );

        $this->validator->validate($query);

        return new JsonDataResponse(
            ReadModelArray::fromItems(
                $this->fetcher->fetch($query)
            )
        );
    }
}
