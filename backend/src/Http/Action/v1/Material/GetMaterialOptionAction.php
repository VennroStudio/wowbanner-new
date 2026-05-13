<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\Router\Route;
use App\Http\Unifier\Material\MaterialOptionUnifier;
use App\Modules\Material\Query\MaterialOption\GetByMaterialIdAndOptionId\MaterialOptionGetByMaterialIdAndOptionIdFetcher;
use App\Modules\Material\Query\MaterialOption\GetByMaterialIdAndOptionId\MaterialOptionGetByMaterialIdAndOptionIdQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/materials/{materialId}/option/{optionId}',
    description: 'Получение опции материала по ID материала и ID опции',
    summary: 'Опция материала',
    security: [['bearerAuth' => []]],
    tags: ['Materials'],
    parameters: [
        new OA\Parameter(
            name: 'materialId',
            description: 'ID материала',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
        new OA\Parameter(
            name: 'optionId',
            description: 'ID опции материала',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Данные опции материала'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 404, description: 'Опция материала не найдена'),
    ]
)]
final readonly class GetMaterialOptionAction implements RequestHandlerInterface
{
    public function __construct(
        private MaterialOptionGetByMaterialIdAndOptionIdFetcher $fetcher,
        private MaterialOptionUnifier $unifier,
    ) {}

    /**
     * @throws JsonException
     * @throws Exception
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $materialId = Route::getArgumentToInt($request, 'materialId');
        $optionId = Route::getArgumentToInt($request, 'optionId');

        $item = $this->fetcher->fetch(
            new MaterialOptionGetByMaterialIdAndOptionIdQuery($materialId, $optionId),
        );

        return new JsonDataResponse($this->unifier->unifyOne(null, $item));
    }
}
