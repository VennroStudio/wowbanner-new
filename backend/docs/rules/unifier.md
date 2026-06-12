# Unifier — сборка HTTP-ответа

Unifier преобразует ReadModel в массив ответа API и подмешивает данные, которые не должны собираться внутри Action.

**Расположение:**
- Unifier: `src/Http/Unifier/{Module}/{Entity}Unifier.php`
- Интерфейс: `src/Components/Http/Unifier/UnifierInterface.php`
- Helper: `src/Components/Http/Unifier/UnifierHelper.php`

---

## Состав Unifier

Unifier собирается только из тех блоков, которые нужны конкретному ответу.

- `final readonly class`
- `UnifierInterface`
- зависимости через конструктор
- `unifyOne()`
- `unify()`
- `map()`
- приватные mapper'ы вложенных частей
- приватные методы преобразования и расчета

Unifier используется для ответов сущностей. Для `select`, `enum`, `permission map` и `success response` он не нужен.

---

## Общий паттерн

Поток всегда один:

1. `Action` вызывает `unifyOne()` или `unify()`
2. `unifyOne()` приводит один объект к общему потоку через `unify()`
3. `unify()` только проходит по списку и вызывает `map()`
4. `map()` собирает HTTP-ответ для одного ReadModel
5. приватные методы собирают вложенные части, убирают технические поля, считают значения

Простой Unifier отличается только тем, что `unify()` сразу вызывает `map()`, а `map()` возвращает `toArray()`.

Сложный Unifier отличается только тем, что `map()` вызывает приватные mapper'ы и fetcher'ы для вложенных частей.

---

## Скелет

```php
<?php

declare(strict_types=1);

namespace App\Http\Unifier\{Module};

use App\Components\Http\Unifier\UnifierHelper;
use App\Components\Http\Unifier\UnifierInterface;
use App\Modules\{Module}\ReadModel\{Entity}\Interface\{Entity}ModelInterface;
use Override;

final readonly class {Entity}Unifier implements UnifierInterface
{
    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if (!$item instanceof {Entity}ModelInterface) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<{Entity}ModelInterface> $items
     * @return list<array<string, mixed>>
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        return array_map($this->map(...), $items);
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function map(object $item): array
    {
        /** @var {Entity}ModelInterface $item */
        $data = $item->toArray();

        return UnifierHelper::withTimestamps($data, $item);
    }
}
```

Если у ответа нет дат, `UnifierHelper::withTimestamps()` не нужен.

---

## Связанные данные

Если ответу нужны связанные данные, они собираются в `map()` или приватных mapper'ах.

```php
#[Override]
public function map(object $item): array
{
    /** @var {Entity}ModelInterface $item */
    $data = $item->toArray();
    $entityId = $item->getId();

    $items = $this->itemFetcher->fetch(
        new {Entity}ItemFindBy{Entity}IdQuery($entityId)
    );
    $services = $this->serviceFetcher->fetch(
        new {Entity}ServiceFindBy{Entity}IdQuery($entityId)
    );

    $data['items'] = $this->mapItems($items);
    $data['services'] = $this->mapServices($services);
    $data['price'] = $this->calculatePrice($services);

    return UnifierHelper::withTimestamps($data, $item);
}
```

---

## Вложенные mapper'ы

Если ответ содержит много вложенных частей, каждая часть выносится в приватный mapper.

Приватный `mapItems()`, `mapFiles()`, `mapServices()` и похожие методы не заменяют основной `map()`. Они вызываются только из `map()` и собирают отдельный вложенный список внутри ответа одного ReadModel.

```php
/**
 * @param list<{Entity}ItemBy{Entity}Id> $items
 * @return list<array<string, mixed>>
 */
private function mapItems(array $items): array
{
    return array_map(function ({Entity}ItemBy{Entity}Id $item): array {
        $data = UnifierHelper::toArrayWithout($item, 'entity_id');
        $children = $this->childFetcher->fetch(
            new {Entity}ChildFindByItemIdQuery($item->id)
        );

        $data['children'] = array_map(
            static fn(object $child): array => UnifierHelper::toArrayWithout($child, 'item_id'),
            $children,
        );

        return $data;
    }, $items);
}
```

Такой mapper вызывается из `map()`:

```php
$data['items'] = $this->mapItems($items);
```

---

## Преобразование полей

Unifier может менять поля только для HTTP-ответа: например, заменить путь файла на публичный URL.

```php
$data = UnifierHelper::transformField(
    $data,
    'path',
    $this->s3Transformer->buildUrl(...),
);
```

---

## Helper

`UnifierHelper` используется для маленьких повторяющихся преобразований.

```php
UnifierHelper::toArrayWithout($item, 'entity_id');
UnifierHelper::withTimestamps($data, $item);
UnifierHelper::transformField($data, 'path', $this->s3Transformer->buildUrl(...));
```

---

## Использование в Action

Action только вызывает Unifier и не знает, как собирается вложенная структура.

```php
return new JsonDataResponse(
    $this->unifier->unifyOne(null, $item)
);
```

```php
return new JsonDataItemsResponse(
    count: $result->count,
    items: $this->unifier->unify(null, $result->items),
);
```
