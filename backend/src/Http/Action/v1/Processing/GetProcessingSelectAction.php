<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Processing;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\ReadModel\ReadModelArray;
use App\Modules\Processing\Query\Processing\GetBySelect\ProcessingGetBySelectFetcher;
use App\Modules\Processing\Query\Processing\GetBySelect\ProcessingGetBySelectQuery;
use Doctrine\DBAL\Exception;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/processings/select',
    description: 'Получение списка обработок для селекта',
    summary: 'Получить обработки для селекта',
    security: [['bearerAuth' => []]],
    tags: ['Processings'],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetProcessingSelectAction implements RequestHandlerInterface
{
    public function __construct(
        private ProcessingGetBySelectFetcher $fetcher,
    ) {}

    /**
     * @throws Exception
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(
            ReadModelArray::fromItems(
                $this->fetcher->fetch(new ProcessingGetBySelectQuery())
            )
        );
    }
}
