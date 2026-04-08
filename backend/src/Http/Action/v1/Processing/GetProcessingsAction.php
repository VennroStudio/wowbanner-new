<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Processing;

use App\Components\Http\Response\JsonDataItemsResponse;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Unifier\Processing\ProcessingUnifier;
use App\Modules\Processing\Query\Processing\FindAll\ProcessingFindAllFetcher;
use App\Modules\Processing\Query\Processing\FindAll\ProcessingFindAllQuery;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Throwable;

#[OA\Get(
    path: '/processings',
    description: 'Получение списка обработок',
    summary: 'Получить список обработок',
    tags: ['Processings'],
    params: [
        new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
        new OA\Parameter(name: 'perPage', in: 'query', schema: new OA\Schema(type: 'integer', default: 20)),
        new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string', nullable: true)),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 422, description: 'Ошибка валидации'),
    ]
)]
final readonly class GetProcessingsAction implements RequestHandlerInterface
{
    public function __construct(
        private Denormalizer $denormalizer,
        private Validator $validator,
        private ProcessingFindAllFetcher $fetcher,
        private ProcessingUnifier $unifier,
    ) {}

    /** @throws Throwable|ExceptionInterface */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->denormalizer->denormalize($request->getQueryParams(), ProcessingFindAllQuery::class);
        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataItemsResponse(
            count: $result->count,
            items: $this->unifier->unify(null, $result->items),
        );
    }
}
