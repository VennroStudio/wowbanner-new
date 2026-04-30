# AGENTS.md

Этот файл обязателен для любого агента, который вносит изменения в проект.

Цель правил: не ломать текущую структуру проекта, не размывать архитектуру и не плодить новые стили организации кода рядом с уже принятыми.

## 1. Базовый порядок работы

Перед началом любой задачи агент обязан:

1. Прочитать `README.md`.
2. Прочитать этот `AGENTS.md`.
3. Изучить существующий код в той зоне, которую он собирается менять.
4. Использовать текущие паттерны проекта, а не придумывать новые без необходимости.
5. После изменений прогнать:

```bash
make verify
```

Задача не считается завершённой, если `make verify` не проходит.

## 2. Архитектурные слои

Проект организован по слоям:

```text
app
pages
widgets
features
entities
shared
```

Агент обязан соблюдать такую иерархию:

- `app` может импортировать всё ниже
- `pages` могут импортировать `widgets`, `features`, `entities`, `shared`
- `widgets` могут импортировать `features`, `entities`, `shared`
- `features` могут импортировать `entities`, `shared`
- `entities` должны опираться на `shared`; зависимости на другие `entities` допустимы только через публичный API и только если это реальная доменная связь
- `shared` не импортирует слои выше

Если решение требует нарушить это правило, агент должен сначала изменить архитектурное правило в отдельной задаче, а не обходить его локально.

## 3. Что нельзя делать

Агенту запрещено:

1. Класть бизнес-логику в `pages`.
2. Импортировать внутренние файлы чужого слайса мимо `index.ts`, если в этом нет строгой локальной необходимости.
3. Писать новые строковые `queryKey` вручную там, где уже есть key factories.
4. Копировать layout modal-окна, footer формы или error banner, если в `shared/ui` уже есть соответствующий примитив.
5. Создавать новый shared helper/component “на будущее”, если реального дублирования ещё нет.
6. Возвращать старые пути и старую структуру каталогов.
7. Делать новый код в стиле, отличающемся от текущего принятых паттернов проекта.

## 4. Правила по каждому слою

### `app`

Здесь допустимы только:

- провайдеры
- bootstrap приложения
- роутер
- глобальный layout

Нельзя:

- класть доменную бизнес-логику
- создавать entity/feature helpers

### `pages`

Page должен быть тонким.

Разрешено:

- собрать экран
- подключить page-state hook из `pages/<name>/model`
- подключить `entities`, `features`, `widgets`

Нельзя:

- держать тяжёлую form-логику
- писать transport mapping
- писать backend-логику
- дублировать CRUD-state, если уже есть shared/page pattern

### `widgets`

Widget — это крупный UI-блок, а не действие пользователя.

Подходит для:

- header
- sidebar
- крупные стабильные layout-блоки

Нельзя превращать `widgets` в склад случайных компонентов.

### `features`

Feature — это пользовательский сценарий.

Подходит для:

- create/edit form
- delete modal
- сценарные таблицы и toolbar-блоки

Если логика привязана к действию пользователя, её место в `features`, а не в `shared`.

### `entities`

Каждая сущность должна придерживаться структуры:

```text
entities/<name>/
  api/
  model/
  index.ts
```

Минимальные правила:

- `api/<name>.api.ts` — HTTP слой
- `model/types.ts` — доменные типы
- `model/query-keys.ts` — key factories
- `model/use...` — query/mutation hooks
- наружу экспортировать через `index.ts`

Нельзя:

- складывать UI в `entities`
- писать логику страниц в `entities`

### `shared`

Только общее и переиспользуемое.

Подходит для:

- UI primitives
- response types
- reusable hooks
- small generic helpers
- config/routing/api client

Нельзя:

- тащить сюда предметную бизнес-логику конкретной сущности
- складывать сюда одноразовые helpers ради “удобства”

## 5. Правила импортов

Снаружи слайса использовать публичный API.

Хорошо:

```ts
import { useClientsQuery } from '@/entities/client';
import { ClientsPage } from '@/pages/clients';
```

Плохо:

```ts
import { useClientsQuery } from '@/entities/client/model/useClientsQuery';
import { ClientsPage } from '@/pages/clients/ui/ClientsPage';
```

Исключение: глубокий импорт допустим только внутри самого слайса.

## 6. Query keys

Агент обязан использовать существующие key factories:

- `clientKeys`
- `materialKeys`
- `processingKeys`
- `printingKeys`

Нельзя снова писать:

```ts
['clients']
['processing', id]
```

Если появляется новая сущность, агент обязан создать её `query-keys.ts` сразу.

## 7. Правила для modal-форм

Если агент создаёт или меняет modal-форму, он обязан использовать текущие shared-паттерны.

### Обязательные примитивы

- `FormErrorBanner`
- `FormModalFooter`
- `useModalFormState`

### Рекомендуемая структура

```text
FeatureFormModal/
  FeatureFormModal.tsx
  lib/
    featureFormSchema.ts
    featureFormMappers.ts
  ui/
    field components...
```

### Правила

1. Schema и default values — в `lib`.
2. Request/view-model mapping — в `lib`.
3. Поля формы — в `ui`.
4. `submitError` и `handleClose` — через `useModalFormState`.
5. Footer — через `FormModalFooter`.
6. Ошибки — через `FormErrorBanner`.

Нельзя копировать старый footer или локальные одинаковые error blocks заново.

## 8. Правила для delete modal

Если агент делает delete modal, он обязан использовать:

- `ConfirmActionModal`

Внутри feature должны остаться только:

- локальное error state
- mutation
- текст описания удаляемого объекта
- `handleClose`

Нельзя заново копировать warning layout, footer и error block.

## 9. Когда можно создавать новый shared primitive

Новый shared primitive можно добавить только если:

1. Дублирование уже есть минимум в 2-3 местах.
2. Новый primitive уменьшает код, а не усложняет его.
3. Он не скрывает важную бизнес-логику.

Если дублирование только в одном месте, агент не должен выносить abstraction “на вырост”.

## 10. Как добавлять новую сущность

Порядок:

1. Создать `entities/<name>`.
2. Сразу добавить:
   - `api/<name>.api.ts`
   - `model/types.ts`
   - `model/query-keys.ts`
   - `model/use...`
   - `index.ts`
3. Только потом подключать `features` и `pages`.

Нельзя начинать с page или feature, если доменная сущность ещё не оформлена.

## 11. Как добавлять новую страницу

Шаблон:

```text
pages/<name>/
  model/
    use<Name>Page.ts
  ui/
    <Name>Page.tsx
  index.ts
```

Правила:

1. Page-state держать в `model`.
2. UI страницы держать в `ui`.
3. Маршрут добавлять в `app/router/AppRouter.tsx`.
4. Если нужен пункт навигации — обновлять sidebar navigation model.

## 12. Как продолжать проект, не ломая структуру

Если агенту поставили новую задачу, он должен идти по такому порядку:

1. Понять, к какому слою относится изменение.
2. Проверить, нет ли уже готового shared/entity/feature паттерна.
3. Внести минимально необходимое изменение внутри текущего слоя.
4. Не тащить логику выше или ниже по слоям “ради удобства”.
5. Прогнать `make verify`.
6. Если появился новый устойчивый паттерн — дописать его в `README.md`.

## 13. Что сейчас особенно важно не ломать

Агент обязан сохранить:

1. lowercase-структуру страниц:
   - `pages/clients`
   - `pages/materials`
   - `pages/processings`
   - `pages/printings`

2. entity-структуру `api + model + index.ts`
3. shared response types в `shared/api/types.ts`
4. shared modal primitives в `shared/ui`
5. page-state hooks в `pages/*/model`
6. query key factories вместо строковых ключей

## 14. Финальный критерий любой задачи

Задача считается выполненной только если:

1. структура проекта не ухудшилась
2. не появился новый дублирующий паттерн
3. `make verify` проходит
4. изменение вписывается в правила этого файла
