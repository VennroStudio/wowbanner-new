<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Processing;

use App\Components\Enum\EnumModel;
use App\Components\Http\Response\JsonDataResponse;
use App\Modules\Processing\Entity\Processing\Fields\Enums\ProcessingType;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/processings/types',
    description: 'Получение списка типов обработки',
    summary: 'Получить типы обработки',
    tags: ['Processings'],
    responses: [
        new OA\Response(response: 200, description: 'Успех'),
    ]
)]
final readonly class GetProcessingTypesAction implements RequestHandlerInterface
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(EnumModel::fromEnumClass(ProcessingType::class));
    }
}
