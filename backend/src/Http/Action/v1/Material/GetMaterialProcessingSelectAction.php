<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Material;

use App\Components\Http\Response\JsonDataResponse;
use App\Components\ReadModel\ReadModelArray;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Modules\Material\Query\MaterialProcessing\GetBySelect\MaterialProcessingGetBySelectFetcher;
use App\Modules\Material\Query\MaterialProcessing\GetBySelect\MaterialProcessingGetBySelectQuery;
use Doctrine\DBAL\Exception;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Get(
    path: '/materials/processings/select',
    description: 'Получение списка обработок по материалу и опции материала для селекта',
    summary: 'Получить обработки материала для селекта',
    security: [['bearerAuth' => []]],
    tags: ['Materials'],
    parameters: [
        new OA\Parameter(
            name: 'materialId',
            description: 'ID материала',
            in: 'query',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
        new OA\Parameter(
            name: 'optionId',
            description: 'ID опции материала',
            in: 'query',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetMaterialProcessingSelectAction implements RequestHandlerInterface
{
    public function __construct(
        private MaterialProcessingGetBySelectFetcher $fetcher,
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
        /** @var MaterialProcessingGetBySelectQuery $query */
        $query = $this->denormalizer->denormalize(
            $request->getQueryParams(),
            MaterialProcessingGetBySelectQuery::class,
        );

        $this->validator->validate($query);

        return new JsonDataResponse(
            ReadModelArray::fromItems(
                $this->fetcher->fetch($query)
            )
        );
    }
}
