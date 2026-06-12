# Permission

**Расположение:**
- `src/Modules/{Module}/Permission/{Module}Permission.php`
- `src/Modules/{Module}/Service/{Module}PermissionService.php`

---

## Состав Permission

Permission собирается только из тех блоков, которые нужны конкретному модулю.

- Permission enum
- PermissionService
- Проверка по роли
- Проверка владельца
- Проверка владельца или роли
- Использование в Handler

---

## Permission enum

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Permission;

enum {Module}Permission: string
{
    case CREATE = '{module}.create';
    case UPDATE = '{module}.update';
    case DELETE = '{module}.delete';
}
```

---

## PermissionService

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\{Module}\Permission\{Module}Permission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class {Module}PermissionService
{
    /**
     * @throws AccessDeniedException
     */
    public function check(UserRole $currentUserRole, {Module}Permission $action): void
    {
        if (!\in_array($currentUserRole, $this->getAllowedRolesForAction($action), true)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @return list<UserRole>
     */
    private function getAllowedRolesForAction({Module}Permission $action): array
    {
        $adminRoles = [
            UserRole::ADMIN,
        ];

        return match ($action) {
            {Module}Permission::CREATE,
            {Module}Permission::UPDATE,
            {Module}Permission::DELETE => $adminRoles,
        };
    }
}
```

---

## Проверка владельца

Добавляется только если владелец может выполнять действия над своим ресурсом.

```php
/**
 * @throws AccessDeniedException
 */
public function checkOwner(int $currentUserId, int $ownerId, {Module}Permission $action): void
{
    if ($currentUserId !== $ownerId) {
        throw new AccessDeniedException();
    }

    if (!\in_array($action, $this->getAllowedActionsForOwner(), true)) {
        throw new AccessDeniedException();
    }
}

/**
 * @return list<{Module}Permission>
 */
private function getAllowedActionsForOwner(): array
{
    return [
        {Module}Permission::UPDATE,
    ];
}
```

---

## Проверка владельца или роли

Добавляется только если действие доступно владельцу или привилегированной роли.

```php
/**
 * @throws AccessDeniedException
 */
public function checkOwnerOrRole(
    int $currentUserId,
    int $ownerId,
    UserRole $currentUserRole,
    {Module}Permission $action,
): void {
    if ($currentUserId === $ownerId) {
        $this->checkOwner($currentUserId, $ownerId, $action);
        return;
    }

    $this->check($currentUserRole, $action);
}
```

---

## Использование в Handler

`currentUserRole` в Command приходит как `int`. В Handler приводится через `UserRole::from()`.

```php
$this->permissionService->check(
    currentUserRole: UserRole::from($command->currentUserRole),
    action: {Module}Permission::UPDATE,
);
```

```php
$this->permissionService->checkOwner(
    currentUserId: $command->currentUserId,
    ownerId: $entity->ownerId,
    action: {Module}Permission::UPDATE,
);
```

```php
$this->permissionService->checkOwnerOrRole(
    currentUserId: $command->currentUserId,
    ownerId: $entity->ownerId,
    currentUserRole: UserRole::from($command->currentUserRole),
    action: {Module}Permission::DELETE,
);
```
