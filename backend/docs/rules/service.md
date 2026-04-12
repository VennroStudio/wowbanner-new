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
    case CREATE = '{module}.create';
    case UPDATE = '{module}.update';
    case DELETE = '{module}.delete';
}
```

### PermissionService

Добавляй только те методы, которые реально нужны для сущности:

- `checkOwner` — владелец может выполнять действия над своим ресурсом, но не все. Требует `getAllowedActionsForOwner()`.
- `checkRole` — действие доступно только по роли, владельца у ресурса нет либо владение не даёт права. Требует `getAllowedRolesForAction()`.
- `checkOwnerOrRole` — действие доступно владельцу ИЛИ привилегированной роли. Делегирует в `checkOwner` и `checkRole`, оба метода должны присутствовать.

`getAllowedActionsForOwner()` — добавляется только если есть `checkOwner`.
`getAllowedRolesForAction()` — добавляется только если есть `checkRole`.

```php
final readonly class {Module}PermissionService
{
    /** 
     * Владелец может выполнять только разрешённые действия над своим ресурсом.
     * @throws AccessDeniedException 
     */
    public function checkOwner(int $currentUserId, int $userId, {Module}Permission $action): void
    {
        if ($currentUserId !== $userId) {
            throw new AccessDeniedException();
        }

        if (!\in_array($action, $this->getAllowedActionsForOwner(), true)) {
            throw new AccessDeniedException();
        }
    }

    /** 
     * Проверка только по роли — владелец не даёт доступа.
     * @throws AccessDeniedException 
     */
    public function checkRole({Module}Role $currentUserRole, {Module}Permission $action): void
    {
        if (!\in_array($currentUserRole, $this->getAllowedRolesForAction($action), true)) {
            throw new AccessDeniedException();
        }
    }

    /** 
     * Владелец (с учётом разрешённых действий) ИЛИ привилегированная роль.
     * @throws AccessDeniedException 
     */
    public function checkOwnerOrRole(int $currentUserId, int $userId, {Module}Role $currentUserRole, {Module}Permission $action): void
    {
        if ($currentUserId === $userId) {
            $this->checkOwner($currentUserId, $userId, $action);
            return;
        }

        $this->checkRole($currentUserRole, $action);
    }

    /** 
     * Действия, которые владелец может выполнять над своим ресурсом.
     * @return list<{Module}Permission> 
     */
    private function getAllowedActionsForOwner(): array
    {
        return [
            {Module}Permission::UPDATE,
        ];
    }

    /** 
     * Роли, которым разрешено действие.
     * @return list<{Module}Role> 
     */
    private function getAllowedRolesForAction({Module}Permission $action): array
    {
        $adminRoles = [
            {Module}Role::ADMIN,
        ];

        return match ($action) {
            {Module}Permission::CREATE,
            {Module}Permission::UPDATE,
            {Module}Permission::DELETE => $adminRoles,
        };
    }
}
```

### Использование в Handler

`currentUserRole` в Command — `int` (приходит из HTTP). В Handler приводится через `{Module}Role::from()`:

```php
// Только владелец (с проверкой разрешённого действия)
$this->permissionService->checkOwner(
    currentUserId: $command->currentUserId,
    userId: $command->userId,
    action: {Module}Permission::UPDATE,
);

// Только роль
$this->permissionService->checkRole(
    currentUserRole: {Module}Role::from($command->currentUserRole),
    action: {Module}Permission::CREATE,
);

// Владелец ИЛИ роль
$this->permissionService->checkOwnerOrRole(
    currentUserId: $command->currentUserId,
    userId: $command->userId,
    currentUserRole: {Module}Role::from($command->currentUserRole),
    action: {Module}Permission::DELETE,
);
```

### Передача идентичности в Action

`currentUserId` и `currentUserRole` **не приходят из тела запроса** — подставляются в Action из идентичности авторизованного пользователя. Примеры — см. [action.md](action.md).