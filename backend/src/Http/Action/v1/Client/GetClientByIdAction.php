<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Client;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\Router\Route;
use App\Http\Unifier\Client\ClientUnifier;
use App\Modules\Client\Query\Client\GetById\ClientGetByIdFetcher;
use App\Modules\Client\Query\Client\GetById\ClientGetByIdQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/clients/{id}',
    description: 'Получение подробной информации о клиенте со списком телефонов и компаний',
    summary: 'Получить клиента по ID',
    security: [['bearerAuth' => []]],
    tags: ['Clients'],
    parameters: [
        new OA\Parameter(name: 'id', description: 'ID клиента', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Данные клиента'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
        new OA\Response(response: 404, description: 'Клиент не найден'),
    ]
)]
final readonly class GetClientByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private ClientGetByIdFetcher $fetcher,
        private ClientUnifier $unifier,
    ) {}

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = Route::getArgumentToInt($request, 'id');
        $item = $this->fetcher->fetch(new ClientGetByIdQuery($id));

        return new JsonDataResponse($this->unifier->unifyOne(null, $item));
    }
}
