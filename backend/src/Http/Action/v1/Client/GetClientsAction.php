<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Client;

use App\Components\Http\Response\JsonDataItemsResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Unifier\Client\ClientUnifier;
use App\Modules\Client\Query\Client\FindAll\ClientFindAllFetcher;
use App\Modules\Client\Query\Client\FindAll\ClientFindAllQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Get(
    path: '/clients',
    description: 'Получение списка всех клиентов с пагинацией и поиском',
    summary: 'Список клиентов',
    security: [['bearerAuth' => []]],
    tags: ['Clients'],
    parameters: [
        new OA\Parameter(name: 'page', description: 'Номер страницы', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        new OA\Parameter(name: 'perPage', description: 'Количество на странице', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
        new OA\Parameter(name: 'search', description: 'Поиск по имени, email, телефону или компании', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Список клиентов'),
        new OA\Response(response: 401, description: 'Требуется авторизация'),
    ]
)]
final readonly class GetClientsAction implements RequestHandlerInterface
{
    public function __construct(
        private ClientFindAllFetcher $fetcher,
        private ClientUnifier $unifier,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws Exception
     * @throws JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->denormalizer->denormalize(
            $request->getQueryParams(),
            ClientFindAllQuery::class
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataItemsResponse(
            count: $result->count,
            items: $this->unifier->unify(null, $result->items),
        );
    }
}
