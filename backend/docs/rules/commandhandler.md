# Command / Handler

**Расположение:** `src/Modules/{Module}/Command/{Entity}/{Action}/`
- `{Action}{Entity}Command.php`
- `{Action}{Entity}Handler.php`

---

## Command

- `final readonly class`, свойства `public` в конструкторе
- Валидация через `#[Assert\...]`, ключи сообщений — `'validation.field_rule'` (ссылаются на переводы)
- Технические поля (`userId`, `currentUserId`, `currentUserRole`) — базовые Assert без кастомного message
- Если команда требует авторизации, те же технические поля добавляются ко **всем** write-командам (включая create), не только к update/delete
- Лимиты — константы в классе
- Внутренние команды (вызов из Handler'а) — без Assert

### Пример: HTTP-команда с валидацией

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Command\{Entity}\{Action};

use Symfony\Component\Validator\Constraints as Assert;

final readonly class {Action}{Entity}Command
{
    private const int NAME_MIN_LENGTH = 2;
    private const int NAME_MAX_LENGTH = 60;
    private const int EMAIL_MAX_LENGTH = 255;
    private const string NAME_PATTERN = "/^\p{L}[\p{L}\s'\-]*$/u";

    public function __construct(
        // Технические поля (подставляются из идентичности в Action, не из тела запроса):
        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public int $currentUserId,

        #[Assert\NotBlank]
        public int $currentUserRole,

        // Пользовательские поля:
        #[Assert\NotBlank(message: 'validation.name_required')]
        #[Assert\Length(
            min: self::NAME_MIN_LENGTH,
            max: self::NAME_MAX_LENGTH,
            minMessage: 'validation.name_too_short',
            maxMessage: 'validation.name_too_long',
        )]
        #[Assert\Regex(pattern: self::NAME_PATTERN, message: 'validation.name_invalid')]
        public string $name,

        #[Assert\NotBlank(message: 'validation.email_required')]
        #[Assert\Email(message: 'validation.email_invalid')]
        #[Assert\Length(max: self::EMAIL_MAX_LENGTH, maxMessage: 'validation.email_too_long')]
        public string $email,

        // Опциональное поле с дефолтом:
        public string $locale = 'en',
    ) {}
}
```

### Пример: внутренняя команда (без Assert)

```php
final readonly class Create{Entity}Command
{
    public function __construct(
        public int $ownerId,
        public {Entity}Type $type,
        public string $hash,
        public DateTimeImmutable $expiresAt,
    ) {}
}
```

---

## Handler

- `final readonly class`, один публичный метод `handle()`
- Возврат: `void` или DTO (например, `TokenPairResult`)
- Декомпозиция в приватные методы — `handle()` читается как сценарий верхнего уровня
- Зависимости через конструктор: Repository, Fetcher, Service, Flusher, другие Handler'ы
- `flush()` — после всех изменений через Repository. Если Handler только вызывает другой Handler, который сам делает flush — свой Flusher не нужен
- Проверка прав — через `PermissionService->check()`: для **create** — в начале `handle()`; для **update/delete** — по образцу User: сначала `getById`, затем `check`, затем изменения
- Инвалидация кеша — после изменения сущности, перед `flush()`

### Принцип: каждая Entity — свои Handler'ы

Если в сценарии нужна операция над другой сущностью — вызывать **её Handler**, не работать с чужим Repository напрямую.

Исключение: массовые операции над связанной сущностью в рамках одного сценария (например, отзыв всех токенов при удалении аккаунта) допустимо выполнять через Repository этой сущности с общим `flush()`.

---

### Пример: Handler без своей записи

Читает через Fetcher, делает проверки, вызывает другой Handler. Flusher не нужен.

```php
final readonly class {Action}Handler
{
    public function __construct(
        private {Entity}FindBy{Field}Fetcher $fetcher,
        private Create{RelatedEntity}Handler $create{RelatedEntity}Handler,
        private SomeService $someService,
    ) {}

    public function handle({Action}Command $command): TokenPairResult
    {
        $entity = $this->authenticate($command);
        $this->validateStatus($entity);
        return $this->issueResult($entity);
    }

    private function authenticate({Action}Command $command): {Entity}By{Field}
    {
        $entity = $this->fetcher->fetchNotDeleted(new {Entity}FindBy{Field}Query($command->field));

        if ($entity === null || !$this->someService->verify($command->secret, $entity->secretHash)) {
            throw new DomainExceptionModule(module: '{module}', message: 'error.invalid_credentials', code: 1);
        }

        return $entity;
    }

    private function validateStatus({Entity}By{Field} $entity): void
    {
        if ($entity->status === {Entity}Status::BANNED) {
            throw new DomainExceptionModule(module: '{module}', message: 'error.account_banned', code: 2);
        }
    }
}
```

---

### Пример: Handler с записью и Flusher

```php
final readonly class Create{Entity}Handler
{
    public function __construct(
        private {Entity}Repository $repository,
        private FlusherInterface $flusher,
    ) {}

    public function handle(Create{Entity}Command $command): void
    {
        $entity = {Entity}::create(
            type: $command->type,
            hash: $command->hash,
            expiresAt: $command->expiresAt,
        );
        $this->repository->add($entity);
        $this->flusher->flush();
    }
}
```

---

### Пример: Handler с правами, кешем и вызовом других Handler'ов

```php
final readonly class Update{Entity}Handler
{
    public function __construct(
        private {Entity}Repository $repository,
        private FlusherInterface $flusher,
        private {Entity}PermissionService $permissionService,
        private Cacher $cacher,
        private Create{RelatedEntity}Handler $create{RelatedEntity}Handler,
    ) {}

    public function handle(Update{Entity}Command $command): void
    {
        $this->permissionService->check(
            currentUserId: $command->currentUserId,
            currentUserRole: {Entity}Role::from($command->currentUserRole),
            entityId: $command->entityId,
            action: {Entity}Permission::UPDATE,
        );

        $entity = $this->repository->getById($command->entityId);

        $this->assertBusinessRule($command);

        $entity->edit(...);

        $this->cacher->delete('{entity}_' . $command->entityId);

        $this->flusher->flush();
    }

    private function assertBusinessRule(Update{Entity}Command $command): void
    {
        // ...
    }
}
```