<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Product;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\Router\Route;
use App\Http\Unifier\Product\ProductUnifier;
use App\Modules\Product\Query\Product\GetById\ProductGetByIdFetcher;
use App\Modules\Product\Query\Product\GetById\ProductGetByIdQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/products/{id}',
    description: 'Получение продукта по ID со списками материалов и печатей',
    summary: 'Получить продукт по ID',
    security: [['bearerAuth' => []]],
    tags: ['Products'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID продукта', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Данные продукта'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 404, description: 'Продукт не найден'),
    ]
)]
final readonly class GetProductByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private ProductGetByIdFetcher $fetcher,
        private ProductUnifier        $unifier,
    ) {}

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = Route::getArgumentToInt($request, 'id');
        $item = $this->fetcher->fetch(new ProductGetByIdQuery($id));

        return new JsonDataResponse($this->unifier->unifyOne(null, $item));
    }
}
