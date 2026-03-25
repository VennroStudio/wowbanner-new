# Service / Permission — Сервисы и права доступа

---

## Service

**Расположение:** `src/Modules/{Module}/Service/{Name}Service.php`

- `final readonly class`, одна зона ответственности
- Зависимости и параметры алгоритма — через конструктор
- Без изменяемого состояния между вызовами

Сервис — вспомогательная утилита, которая выносит повторяющуюся или специфичную логику из Handler'а. Может быть чем угодно: хешер, генератор, калькулятор, враппер над внешним API и т.д. Ниже — примеры типичных паттернов.

```php
final readonly class {Name}HasherService
{
    public function __construct(
        private int $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
        private int $timeCost   = PASSWORD_ARGON2_DEFAULT_TIME_COST,
        private int $threads    = PASSWORD_ARGON2_DEFAULT_THREADS,
    ) {}

    public function hash(string $value): string
    {
        return password_hash($value, PASSWORD_ARGON2I, [
            'memory_cost' => $this->memoryCost,
            'time_cost'   => $this->timeCost,
            'threads'     => $this->threads,
        ]);
    }

    public function verify(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }
}
```

```php
final readonly class {Name}TokenHasherService
{
    /** @throws RandomException */
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    public function verify(string $token, string $hash): bool
    {
        return hash_equals($this->hash($token), $hash);
    }
}
```

---

## Permission

Вводится если операции доступны не всем — только владельцу ресурса или определённым ролям.

**Расположение:**
- `src/Modules/{Module}/Permission/{Module}Permission.php`
- `src/Modules/{Module}/Service/{Module}PermissionService.php`

### Permission enum

`string`-backed, значения вида `'{module}.{action}'`:

```php
enum {Module}Permission: string
{
    case UPDATE = '{module}.update';
    case DELETE = '{module}.delete';
}
```

### PermissionService

Логика: `$currentUserId === $resourceOwnerId` → доступ разрешён (свой ресурс). Иначе — проверка роли через `getAllowedRolesForAction()`.

```php
final readonly class {Module}PermissionService
{
    /** @throws AccessDeniedException */
    public function check(int $currentUserId, {Module}Role $currentUserRole, int $ownerId, {Module}Permission $action): void
    {
        if (!$this->hasAccess($currentUserId, $currentUserRole, $ownerId, $action)) {
            throw new AccessDeniedException();
        }
    }

    public function hasAccess(int $currentUserId, {Module}Role $currentUserRole, int $ownerId, {Module}Permission $action): bool
    {
        if ($currentUserId === $ownerId) {
            return true;
        }

        return in_array($currentUserRole, $this->getAllowedRolesForAction($action), true);
    }

    /** @return list<{Module}Role> */
    private function getAllowedRolesForAction({Module}Permission $action): array
    {
        return match ($action) {
            {Module}Permission::UPDATE,
            {Module}Permission::DELETE => [{Module}Role::ADMIN],
        };
    }
}
```

### Использование в Handler

`currentUserRole` в Command — `int` (приходит из HTTP). В Handler приводится через `{Module}Role::from()`:

```php
$this->permissionService->check(
    currentUserId: $command->currentUserId,
    currentUserRole: {Module}Role::from($command->currentUserRole),
    ownerId: $command->entityId,
    action: {Module}Permission::UPDATE,
);
```

### Передача идентичности в Action

`currentUserId` и `currentUserRole` **не приходят из тела запроса** — подставляются в Action из идентичности авторизованного пользователя. Примеры — см. [action.md](action.md).