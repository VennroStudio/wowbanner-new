<?php

declare(strict_types=1);

namespace App\Http\Action\v1\Client;

use App\Components\Enum\EnumModel;
use App\Components\Http\Response\JsonDataResponse;
use App\Modules\Client\Entity\ClientPhone\Fields\PhoneType;
use JsonException;
use OpenApi\Attributes as OA;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/clients/phone-types',
    description: 'Справочник типов номера телефона (основной / дополнительный)',
    summary: 'Типы телефонов клиента',
    security: [['bearerAuth' => []]],
    tags: ['Clients'],
    responses: [
        new OA\Response(response: 200, description: 'Успешный ответ со списком'),
        new OA\Response(response: 401, description: 'Не авторизован'),
    ]
)]
final readonly class GetClientPhoneTypesAction implements RequestHandlerInterface
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonDataResponse(EnumModel::fromEnumClass(PhoneType::class));
    }
}
