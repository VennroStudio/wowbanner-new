<?php

declare(strict_types=1);

namespace App\Http\Action\v1\User;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\ReadModel\ReadModelArray;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\User\Query\User\GetBySelect\UserGetBySelectFetcher;
use App\Modules\User\Query\User\GetBySelect\UserGetBySelectQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Get(
    path: '/users/select',
    description: 'Получение списка пользователей для селекта с необязательным фильтром по роли',
    summary: 'Получить пользователей для селекта',
    security: [['bearerAuth' => []]],
    tags: ['Users'],
    parameters: [
        new OA\Parameter(
            name: 'role',
            description: 'Фильтр по роли пользователя',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetUserSelectAction implements RequestHandlerInterface
{
    public function __construct(
        private UserGetBySelectFetcher $fetcher,
        private Denormalizer $denormalizer,
        private Validator $validator,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws Exception
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var UserGetBySelectQuery $query */
        $query = $this->denormalizer->denormalize(
            $request->getQueryParams(),
            UserGetBySelectQuery::class,
        );

        $this->validator->validate($query);

        return new JsonDataResponse(
            ReadModelArray::fromItems(
                $this->fetcher->fetch($query)
            )
        );
    }
}
