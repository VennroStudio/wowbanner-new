<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\ReadModel\ReadModelArray;
use App\Modules\Material\Query\Material\GetBySelect\MaterialGetBySelectFetcher;
use App\Modules\Material\Query\Material\GetBySelect\MaterialGetBySelectQuery;
use Doctrine\DBAL\Exception;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/materials/select',
    description: 'Получение списка материалов для селекта',
    summary: 'Получить материалы для селекта',
    security: [['bearerAuth' => []]],
    tags: ['Materials'],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetMaterialSelectAction implements RequestHandlerInterface
{
    public function __construct(
        private MaterialGetBySelectFetcher $fetcher,
    ) {}

    /**
     * @throws Exception
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(
            ReadModelArray::fromItems(
                $this->fetcher->fetch(new MaterialGetBySelectQuery())
            )
        );
    }
}
