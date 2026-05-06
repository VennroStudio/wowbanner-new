<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\ReadModel\ReadModelArray;
use App\Components\Router\Route;
use App\Modules\Material\Query\MaterialOption\GetBySelect\MaterialOptionGetBySelectFetcher;
use App\Modules\Material\Query\MaterialOption\GetBySelect\MaterialOptionGetBySelectQuery;
use Doctrine\DBAL\Exception;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/materials/{id}/options/select',
    description: 'Получение списка вариантов материала для селекта',
    summary: 'Получить варианты материала для селекта',
    security: [['bearerAuth' => []]],
    tags: ['Materials'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID материала',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetMaterialOptionSelectAction implements RequestHandlerInterface
{
    public function __construct(
        private MaterialOptionGetBySelectFetcher $fetcher,
    ) {}

    /**
     * @throws Exception
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(
            ReadModelArray::fromItems(
                $this->fetcher->fetch(
                    new MaterialOptionGetBySelectQuery(
                        Route::getArgumentToInt($request, 'id')
                    )
                )
            )
        );
    }
}
