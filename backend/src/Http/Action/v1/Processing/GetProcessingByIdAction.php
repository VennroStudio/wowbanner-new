<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Processing;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\Http\Route\Route;
use App\Http\Unifier\Processing\ProcessingUnifier;
use App\Modules\Processing\Query\Processing\GetById\ProcessingGetByIdFetcher;
use App\Modules\Processing\Query\Processing\GetById\ProcessingGetByIdQuery;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

#[OA\Get(
    path: '/processings/{id}',
    description: 'Получение обработки по ID',
    summary: 'Получить обработку по ID',
    tags: ['Processings'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer'),
            description: 'ID обработки'
        )
    ],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 404, description: 'Обработка не найдена'),
    ]
)]
final readonly class GetProcessingByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private ProcessingGetByIdFetcher $fetcher,
        private ProcessingUnifier $unifier,
    ) {}

    /** @throws Throwable */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = Route::getArgumentToInt($request, 'id');
        $item = $this->fetcher->fetch(new ProcessingGetByIdQuery($id));

        return new JsonDataResponse($this->unifier->unifyOne(null, $item));
    }
}
