# Cache

**Расположение:**
- Query / Fetcher: `src/Modules/{Module}/Query/{Entity}/{Action}/`
- Invalidator, если нужен: `src/Modules/{Module}/Service/{Module}QueryCacheInvalidator.php`

---

## Состав Cache

Кеш собирается только из тех блоков, которые нужны конкретному query-сценарию.

- `Cacher`
- `CACHE_TTL`
- Cache key
- Чтение из кеша
- Запись в кеш
- Разовое удаление ключа в Handler
- QueryCacheInvalidator
- Использование invalidator в Handler

---

## Кеширование в Fetcher

Кеширование API-чтения выполняется в Query / Fetcher.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Query\{Entity}\GetById;

use App\Components\Cacher\Cacher;
use App\Components\Exception\DomainExceptionModule;
use App\Modules\{Module}\ReadModel\{Entity}\{Entity}ById;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class {Entity}GetByIdFetcher
{
    private const int CACHE_TTL = 900;

    public function __construct(
        private Connection $connection,
        private Cacher $cacher,
    ) {}

    /**
     * @throws Exception
     */
    public function fetch({Entity}GetByIdQuery $query): {Entity}ById
    {
        $key = '{entity}_by_id_' . $query->id;

        /** @var {Entity}ById|null $cached */
        $cached = $this->cacher->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $row = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from('{table_name}')
            ->where('id = :id')
            ->setParameter('id', $query->id)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw new DomainExceptionModule(
                module: '{module}',
                message: 'error.{entity}_not_found',
                code: 1
            );
        }

        $result = {Entity}ById::fromRow($row);
        $this->cacher->set($key, $result, self::CACHE_TTL);

        return $result;
    }
}
```

---

## Cache key

Cache key должен повторять смысл Fetcher'а.

```php
$key = '{entity}_by_id_' . $query->id;
```

```php
$key = '{entity}_by_parent_id_' . $query->parentId;
```

```php
$key = '{entity}_by_parent_id_' . $query->parentId . '_child_id_' . $query->childId;
```

---

## QueryCacheInvalidator

Используется, когда один write-сценарий затрагивает несколько query-cache ключей или связанный контекст.

Если нужно удалить один очевидный ключ текущей сущности, можно использовать `Cacher` прямо в Handler.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Module}\Service;

use App\Components\Cacher\Cacher;

final readonly class {Module}QueryCacheInvalidator
{
    public function __construct(
        private Cacher $cacher,
    ) {}

    public function invalidateById(int $id): void
    {
        $this->cacher->delete('{entity}_by_id_' . $id);
    }

    public function invalidateByParentId(int $parentId): void
    {
        $this->cacher->delete('{entity}_by_parent_id_' . $parentId);
    }

    public function invalidateParentAndChildContext(int $parentId, int $childId): void
    {
        $suffix = $parentId . '_child_id_' . $childId;

        $this->cacher->delete('{entity}_options_by_parent_id_' . $suffix);
        $this->cacher->delete('{entity}_prices_by_parent_id_' . $suffix);
        $this->cacher->delete('{entity}_processings_by_parent_id_' . $suffix);
    }
}
```

---

## Разовое удаление ключа в Handler

Подходит для простого кеша одной read-модели: `getById`, identity, token, одиночный справочник.

```php
use App\Components\Cacher\Cacher;

final readonly class Update{Entity}Handler
{
    public function __construct(
        private Cacher $cacher,
    ) {}

    public function handle(Update{Entity}Command $command): void
    {
        // ...

        $this->cacher->delete('{entity}_by_id_' . $command->id);
    }
}
```

---

## Использование QueryCacheInvalidator в Handler

Подходит для связанного кеша: дочерние сущности, списки по parent id, контекст `parentId + childId`, несколько read-моделей, повторяющаяся инвалидация в разных Handler'ах.

```php
public function __construct(
    private {Module}QueryCacheInvalidator $queryCacheInvalidator,
) {}

public function handle(Update{Entity}Command $command): void
{
    // ...

    $this->queryCacheInvalidator->invalidateById($command->id);
}
```

```php
$this->queryCacheInvalidator->invalidateByParentId($parentId);
$this->queryCacheInvalidator->invalidateParentAndChildContext($parentId, $childId);
```

Можно комбинировать оба подхода: удалить основной ключ напрямую и вызвать invalidator для связанных query-cache ключей.

```php
$this->cacher->delete('{entity}_by_id_' . $entityId);
$this->queryCacheInvalidator->invalidateByParentId($entityId);
```
