<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\Router\Route;
use App\Http\Unifier\Material\MaterialUnifier;
use App\Modules\Material\Query\Material\GetById\MaterialGetByIdFetcher;
use App\Modules\Material\Query\Material\GetById\MaterialGetByIdQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/materials/{id}',
    description: 'Получение материала по ID',
    summary: 'Материал по ID',
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
        new OA\Response(response: 200, description: 'Данные материала'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 404, description: 'Материал не найден'),
    ]
)]
final readonly class GetMaterialByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private MaterialGetByIdFetcher $fetcher,
        private MaterialUnifier $unifier,
    ) {}

    /**
     * @throws JsonException
     * @throws Exception
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = Route::getArgumentToInt($request, 'id');

        $material = $this->fetcher->fetch(new MaterialGetByIdQuery($id));

        return new JsonDataResponse($this->unifier->unifyOne(null, $material));
    }
}
