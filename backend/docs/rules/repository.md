# Repository — Репозиторий

Только для **write-операций**. Чтение — исключительно через Fetcher/Query.

**Расположение:**
- Интерфейс: `src/Modules/{Module}/Entity/{Entity}/{Entity}Repository.php`
- Реализация: `src/Modules/{Module}/Entity/{Entity}/Persistence/Doctrine/Doctrine{Entity}Repository.php`
- Регистрация: `config/common/repositories.php` — `{Entity}Repository::class => get(Doctrine{Entity}Repository::class)`

---

## Правила

- **`add()`** — persist сущности (без flush), параметр именуется по сущности
- **`remove()`** — только при необходимости hard delete
- **`getById()`** — для write; бросает `DomainExceptionModule` если не найден
- **`findById()`** — для write; возвращает `null` если не найден
- Дополнительные методы поиска — только под реальные write-сценарии
- Flush — через `FlusherInterface` в Handler'е, не в репозитории
- `getById`/`findById` — только для изменения сущности в Handler'е, не для ответа клиенту

---

## Интерфейс

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Entity\{Entity};

interface {Entity}Repository
{
    public function add({Entity} ${entity}): void;

    public function getById(int $id): {Entity};

    public function findById(int $id): ?{Entity};

    // При необходимости hard delete:
    // public function remove({Entity} ${entity}): void;

    // Дополнительные методы под write-сценарии:
    // public function findByOwnerAndType(int $ownerId, {Entity}Type $type): array;
}
```

---

## Doctrine-реализация

`final class`. Единственная зависимость — `EntityManagerInterface`; `EntityRepository` создаётся в конструкторе. Все методы интерфейса помечаются `#[Override]`.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Entity\{Entity}\Persistence\Doctrine;

use App\Components\Exception\DomainExceptionModule;
use App\Modules\{Module}\Entity\{Entity}\{Entity};
use App\Modules\{Module}\Entity\{Entity}\{Entity}Repository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class Doctrine{Entity}Repository implements {Entity}Repository
{
    /** @var EntityRepository<{Entity}> */
    private EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository({Entity}::class);
    }

    #[Override]
    public function add({Entity} ${entity}): void
    {
        $this->em->persist(${entity});
    }

    #[Override]
    public function getById(int $id): {Entity}
    {
        if (!${entity} = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: '{module}',
                message: 'error.{entity}_not_found',
                code: 1
            );
        }

        return ${entity};
    }

    #[Override]
    public function findById(int $id): ?{Entity}
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    // При необходимости hard delete:
    // #[Override]
    // public function remove({Entity} ${entity}): void
    // {
    //     $this->em->remove(${entity});
    // }
}
```