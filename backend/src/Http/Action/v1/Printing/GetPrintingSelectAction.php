<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Printing;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\ReadModel\ReadModelArray;
use App\Modules\Printing\Query\Printing\GetBySelect\PrintingGetBySelectFetcher;
use App\Modules\Printing\Query\Printing\GetBySelect\PrintingGetBySelectQuery;
use Doctrine\DBAL\Exception;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/printings/select',
    description: 'Получение списка печатей для селекта',
    summary: 'Получить печати для селекта',
    security: [['bearerAuth' => []]],
    tags: ['Printings'],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetPrintingSelectAction implements RequestHandlerInterface
{
    public function __construct(
        private PrintingGetBySelectFetcher $fetcher,
    ) {}

    /**
     * @throws Exception
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(
            ReadModelArray::fromItems(
                $this->fetcher->fetch(new PrintingGetBySelectQuery())
            )
        );
    }
}
