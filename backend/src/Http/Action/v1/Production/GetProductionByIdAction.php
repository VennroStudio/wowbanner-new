<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Production;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\Router\Route;
use App\Http\Unifier\Production\ProductionUnifier;
use App\Modules\Production\Query\Production\GetById\ProductionGetByIdFetcher;
use App\Modules\Production\Query\Production\GetById\ProductionGetByIdQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/productions/{id}',
    description: 'Получение производства по ID со списками материалов и печатей',
    summary: 'Получить производство по ID',
    security: [['bearerAuth' => []]],
    tags: ['Productions'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID производства', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Данные производства'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 404, description: 'Производство не найдено'),
    ]
)]
final readonly class GetProductionByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private ProductionGetByIdFetcher $fetcher,
        private ProductionUnifier $unifier,
    ) {}

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = Route::getArgumentToInt($request, 'id');
        $item = $this->fetcher->fetch(new ProductionGetByIdQuery($id));

        return new JsonDataResponse($this->unifier->unifyOne(null, $item));
    }
}
