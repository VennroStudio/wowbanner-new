# Service

**Расположение:** `src/Modules/{Module}/Service/{Name}Service.php`

---

## Состав Service

Service собирается только из тех блоков, которые нужны конкретному сценарию.

- Обычный Service
- SyncerService для связанных сущностей
- Storage / File Service
- Вызов Handler'ов подсущностей
- Приватные методы алгоритма

PermissionService описывается отдельно в [Permission](permission.md).

QueryCacheInvalidator описывается отдельно в [Cache](cache.md).

---

## Обычный Service

Используется для повторяемой логики, внешнего клиента, хеширования, генерации, расчета или технической операции.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Service;

final readonly class {Name}HasherService
{
    public function hash(string $value): string
    {
        return password_hash($value, PASSWORD_ARGON2I);
    }

    public function verify(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }
}
```

---

## SyncerService

Используется, когда основная сущность имеет связанные таблицы, которые нужно синхронизировать как часть одного сценария.

Пример: `{Entity}` и `{Entity}Image`, `{Entity}` и `{Entity}Option`, `{Entity}` и `{Entity}Price`.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Service;

use App\Modules\{Module}\Command\{ChildEntity}\Create\Create{ChildEntity}Command;
use App\Modules\{Module}\Command\{ChildEntity}\Create\Create{ChildEntity}Handler;
use App\Modules\{Module}\Command\{ChildEntity}\Delete\Delete{ChildEntity}Command;
use App\Modules\{Module}\Command\{ChildEntity}\Delete\Delete{ChildEntity}Handler;
use App\Modules\{Module}\Command\{ChildEntity}\Update\Update{ChildEntity}Command;
use App\Modules\{Module}\Command\{ChildEntity}\Update\Update{ChildEntity}Handler;
use App\Modules\{Module}\Entity\{ChildEntity}\{ChildEntity}Repository;

final readonly class {Entity}StructureSyncerService
{
    public function __construct(
        private {ChildEntity}Repository $childRepository,
        private Create{ChildEntity}Handler $createHandler,
        private Update{ChildEntity}Handler $updateHandler,
        private Delete{ChildEntity}Handler $deleteHandler,
    ) {}

    /**
     * @param list<{ChildEntity}Payload> $children
     */
    public function sync(int $entityId, array $children): void
    {
        $existing = $this->childRepository->findByEntityId($entityId);
        $incomingIds = [];

        foreach ($children as $child) {
            if ($child->id === null) {
                $this->createHandler->handle(
                    new Create{ChildEntity}Command(
                        entityId: $entityId,
                        name: $child->name,
                        sort: $child->sort,
                    )
                );

                continue;
            }

            $incomingIds[] = $child->id;

            $this->updateHandler->handle(
                new Update{ChildEntity}Command(
                    id: $child->id,
                    name: $child->name,
                    sort: $child->sort,
                )
            );
        }

        foreach ($existing as $entity) {
            if (!\in_array((int) $entity->id, $incomingIds, true)) {
                $this->deleteHandler->handle(
                    new Delete{ChildEntity}Command(id: (int) $entity->id)
                );
            }
        }
    }
}
```

SyncerService координирует сценарий, но состояние Entity меняется через методы самой Entity внутри соответствующих Handler'ов.

---

## Storage / File Service

Используется для работы с файлами, путями, внешним хранилищем и заменой старого файла новым.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Service;

use App\Components\Storage\StorageInterface;
use App\Modules\{Module}\Entity\{Entity}\Fields\Enums\{Entity}Directory;
use Psr\Http\Message\UploadedFileInterface;

final readonly class {Entity}FileStorageService
{
    public function __construct(
        private StorageInterface $storage,
    ) {}

    public function upload(int $entityId, UploadedFileInterface $file): string
    {
        return $this->storage->upload(
            file: $file,
            directory: {Entity}Directory::FILES->getPath($entityId),
        );
    }

    public function replace(?string $oldPath, string $newPath): void
    {
        if ($oldPath !== null) {
            $this->storage->delete($oldPath);
        }
    }
}
```

Enum-директории описаны отдельно в [Enum](enum.md).

---

## Использование в Handler

```php
public function __construct(
    private {Entity}StructureSyncerService $structureSyncer,
) {}

public function handle(Update{Entity}Command $command): void
{
    // ...

    $this->structureSyncer->sync($command->id, $command->children);
}
```
