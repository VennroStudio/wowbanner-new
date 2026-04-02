<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Printing;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\Router\Route;
use App\Http\Unifier\Printing\PrintingUnifier;
use App\Modules\Printing\Query\Printing\GetById\PrintingGetByIdFetcher;
use App\Modules\Printing\Query\Printing\GetById\PrintingGetByIdQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/printings/{id}',
    description: 'Получение записи печати по ID',
    summary: 'Печать по ID',
    security: [['bearerAuth' => []]],
    tags: ['Printings'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID записи печати',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Данные записи'),
        new OA\Response(response: 401, description: 'Не авторизован'),
        new OA\Response(response: 404, description: 'Запись не найдена'),
    ]
)]
final readonly class GetPrintingByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private PrintingGetByIdFetcher $fetcher,
        private PrintingUnifier $unifier,
    ) {}

    /**
     * @throws JsonException
     * @throws Exception
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = Route::getArgumentToInt($request, 'id');

        $printing = $this->fetcher->fetch(new PrintingGetByIdQuery($id));

        return new JsonDataResponse($this->unifier->unifyOne(null, $printing));
    }
}
