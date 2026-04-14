<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Client;

use App\Components\Enum\EnumModel;
use App\Components\Http\Response\JsonDataResponse;
use App\Modules\Client\Entity\Client\Fields\Docs;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/clients/docs-types',
    description: 'Справочник типов документов (ЭДО / доверенность / Б/Д)',
    summary: 'Типы документов клиента',
    security: [['bearerAuth' => []]],
    tags: ['Clients'],
    responses: [
        new OA\Response(response: 200, description: 'Успешный ответ со списком'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetClientDocsTypesAction implements RequestHandlerInterface
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(EnumModel::fromEnumClass(Docs::class));
    }
}
