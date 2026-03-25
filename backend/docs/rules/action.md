# Action — HTTP-обработчик

Точка входа HTTP: денормализация запроса → валидация → Handler/Fetcher → ответ.

**Расположение:**
- Action: `src/Http/Action/v1/{Entity}/{Action}{Entity}Action.php`
- Unifier: `src/Http/Unifier/{Entity}/{Entity}Unifier.php`
- Маршруты: `config/routes/v1.php`

---

## Правила

- `final readonly class`, реализует `RequestHandlerInterface`
- Один метод `handle(ServerRequestInterface $request): ResponseInterface`
- **OpenAPI атрибуты** (`#[OA\Get]`, `#[OA\Post]`, `#[OA\Patch]`, `#[OA\Delete]`) — обязательны на каждом Action для генерации документации
- **POST/PATCH/DELETE:** `denormalize` → `validate` → `handle` → Response
- **GET:** `denormalize` (query params) → `validate` → `fetch` → `unify` → Response
- Body: `(array) $request->getParsedBody()`, query: `$request->getQueryParams()`
- Дополнительные поля (userId, currentUserId, currentUserRole, locale) подставляются через `array_merge()`
- Параметр маршрута: `Route::getArgumentToInt($request, 'id')`
- Защищённый маршрут: `RequestIdentity::get($request)`
- Имена полей JSON совпадают с именами свойств Command/Query (camelCase)

---

## Responses

| Класс | Когда |
|-------|-------|
| `JsonDataResponse($data, $status)` | Один объект или скаляр; по умолчанию 200 |
| `JsonDataItemsResponse(count: $n, items: $list)` | Список с количеством; `$list` — массив после Unifier |
| `JsonDataSuccessResponse()` | Успех без тела, **201** |
| `JsonDataSuccessResponse(1, 200)` | Успех без тела, **200** (update/delete) |
| `new Response(204)` | Logout и аналоги; к нему применяется `cookieManager->discard()` |

---

## Unifier

Преобразует ReadModel (или список) в массив для ответа API: вызывает `toArray()`, подставляет URL для файлов (S3), подмешивает связанные сущности. Используется во всех Action, возвращающих данные сущности.

**Интерфейс:** `App\Components\Http\Unifier\UnifierInterface`

```php
interface UnifierInterface
{
    public function unifyOne(?int $userId, ?object $item): array;

    /** @param list<object> $items
     *  @return list<array<string,mixed>> */
    public function unify(?int $userId, array $items): array;

    public function map(object $item): array;
}
```

### Простой Unifier (один тип сущности)

```php
final readonly class {Entity}Unifier implements UnifierInterface
{
    public function __construct(
        private S3Transformer $s3Transformer,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if ($item === null) return [];
        return $this->unify($userId, [$item])[0] ?? [];
    }

    /** @param list<{Entity}ModelInterface> $items
     *  @return list<array<string,mixed>> */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) return [];
        return array_map(fn({Entity}ModelInterface $item): array => $this->map($item), $items);
    }

    #[Override]
    public function map(object $item): array
    {
        $data = $item->toArray();
        $data['file'] = $this->s3Transformer->buildUrl($data['file']);
        return $data;
    }
}
```

### Unifier с подгрузкой связанных сущностей

Если ответ должен содержать связанные данные (например, Entity + Profile + Items) — Unifier инжектирует нужные Fetcher'ы, делает один запрос по всем id, группирует по `ownerId` и подмешивает в `map()`. Так избегается N+1.

```php
final readonly class {Entity}Unifier implements UnifierInterface
{
    public function __construct(
        private S3Transformer $s3Transformer,
        private {Related}Fetcher $relatedFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if ($item === null) return [];
        return $this->unify($userId, [$item])[0] ?? [];
    }

    /** @param list<{Entity}ModelInterface> $items
     *  @return list<array<string,mixed>> */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) return [];

        $ids = array_map(static fn({Entity}ModelInterface $i): int => $i->getId(), $items);
        $related = $this->groupRelated($this->relatedFetcher->fetchByOwnerIds($ids));

        return array_map(fn({Entity}ModelInterface $item): array => $this->map($item, $related), $items);
    }

    #[Override]
    public function map(object $item, array $related = []): array
    {
        $data = $item->toArray();
        $data['file']    = $this->s3Transformer->buildUrl($data['file']);
        $data['related'] = $related[$item->getId()] ?? [];
        return $data;
    }

    /** @param list<{Related}ModelInterface> $items
     *  @return array<int, array<string,mixed>> */
    private function groupRelated(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->getOwnerId()][] = $item->toArray();
        }
        return $grouped;
    }
}
```

---

## Примеры Action

### GET — один объект

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $id = Route::getArgumentToInt($request, 'id');
    $item = $this->fetcher->fetch(new {Entity}GetByIdQuery($id));
    return new JsonDataResponse($this->unifier->unifyOne(null, $item));
}
```

### GET — список

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $query = $this->denormalizer->denormalize($request->getQueryParams(), {Entity}FindAllQuery::class);
    $this->validator->validate($query);

    $result = $this->fetcher->fetch($query);

    return new JsonDataItemsResponse(
        count: $result->count,
        items: $this->unifier->unify(null, $result->items),
    );
}
```

### POST — создание

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $command = $this->denormalizer->denormalize(
        array_merge((array) $request->getParsedBody(), [
            'locale' => $this->translator->getLocale(),
        ]),
        Create{Entity}Command::class,
    );
    $this->validator->validate($command);
    $this->handler->handle($command);
    return new JsonDataSuccessResponse();
}
```

### PATCH — обновление (защищённый)

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $identity = RequestIdentity::get($request);

    $command = $this->denormalizer->denormalize(
        array_merge((array) $request->getParsedBody(), [
            'entityId'        => Route::getArgumentToInt($request, 'id'),
            'currentUserId'   => $identity->id,
            'currentUserRole' => $identity->role->value,
        ]),
        Update{Entity}Command::class,
    );
    $this->validator->validate($command);
    $this->handler->handle($command);
    return new JsonDataSuccessResponse(1, 200);
}
```

### DELETE — без body

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $identity = RequestIdentity::get($request);

    $command = $this->denormalizer->denormalize([
        'entityId'        => Route::getArgumentToInt($request, 'id'),
        'currentUserId'   => $identity->id,
        'currentUserRole' => $identity->role->value,
    ], Delete{Entity}Command::class);
    $this->validator->validate($command);
    $this->handler->handle($command);
    return new JsonDataSuccessResponse(1, 200);
}
```

### POST — файл (multipart)

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $identity = RequestIdentity::get($request);
    $file = RequestFile::extract($request, 'file');

    if ($file === null) {
        throw new DomainExceptionModule(module: '{module}', message: 'error.file_required', code: 1);
    }

    $url = $this->handler->handle(new Upload{Entity}FileCommand(
        entityId:        Route::getArgumentToInt($request, 'id'),
        currentUserId:   $identity->id,
        currentUserRole: $identity->role->value,
        tmpFilePath:     $file->getPath(),
    ));
    return new JsonDataResponse(['file' => $url]);
}
```

### POST — ответ с Cookie (login/refresh)

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    // ...denormalize, validate...
    $result = $this->handler->handle($command);

    $response = new JsonDataResponse([
        'access_token' => $result->accessToken,
        'expires_in'   => $result->expiresIn,
    ]);
    return $this->cookieManager->apply(
        response: $response,
        context: new CookieContext(refreshToken: $result->refreshToken),
    );
}
```

### POST — данные из Cookie (logout)

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $cookies = RequestCookies::get($request);
    $this->handler->handle(new LogoutCommand(refreshToken: $cookies->refreshToken));
    return $this->cookieManager->discard(new Response(204), new CookieContext());
}
```

---

## Регистрация маршрутов

`config/routes/v1.php`:

```php
$app->group('/v1', new Group(static function (RouteCollectorProxy $group): void {
    $group->get('', OpenApiAction::class); // Swagger UI / OpenAPI spec

    $group->group('/{entities}', new Group(static function (RouteCollectorProxy $group): void {
        $group->get('', GetAll{Entity}Action::class)->add(Authenticate::class);
        $group->post('/create', Create{Entity}Action::class);
        $group->get('/{id}', Get{Entity}ByIdAction::class);
        $group->patch('/update/{id}', Update{Entity}Action::class)->add(Authenticate::class);
        $group->delete('/delete/{id}', Delete{Entity}Action::class)->add(Authenticate::class);
        $group->post('/{id}/file', Upload{Entity}FileAction::class)->add(Authenticate::class);
    }));

    $group->group('/auth', new Group(static function (RouteCollectorProxy $group): void {
        $group->post('/login', LoginAction::class);
        $group->post('/refresh', RefreshTokenAction::class)->add(ExtractCookies::class);
        $group->post('/logout', LogoutAction::class)->add(ExtractCookies::class);
    }));
}));
```

- `->add(Authenticate::class)` — маршрут требует JWT (`RequestIdentity` доступен в Action)
- `->add(ExtractCookies::class)` — извлечение cookie перед обработчиком (refresh/logout)
- `GET /v1` — отдаёт OpenAPI спецификацию (Swagger)