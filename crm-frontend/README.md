# crm-frontend-wowbanner

React + TypeScript фронтенд. Сборка через Vite, окружение через Docker.

## Базовая структура проекта

```text
crm-frontend-wowbanner/
├── docker/
│   ├── development/
│   │   └── Dockerfile                 # dev-контейнер (Vite dev server)
│   └── production/
│       ├── Dockerfile                 # prod-образ (сборка + nginx)
│       └── nginx.conf                 # конфиг nginx для SPA
├── src/                               # исходный код
├── .env                               # переменные окружения (не коммитить)
├── .env.deploy                        # переменные для деплоя (не коммитить)
├── .env.example                       # шаблон .env
├── docker-compose.yml                 # dev-окружение
├── docker-compose-production.yml      # prod-окружение
├── Makefile                           # основные команды проекта
└── README.md
```

## Первый запуск

### 1. Поднять контейнер

```bash
make init
```

### 2. Перезапуск / пересборка

```bash
make restart
make rebuild
```

## Основные команды

Работать нужно через контейнер:

```bash
make docker-up        # поднять dev-контейнер
make docker-down      # остановить контейнеры
make restart          # перезапустить
make rebuild          # пересобрать и поднять заново
make docker-shell     # зайти внутрь контейнера
make docker-logs      # смотреть логи

make install          # npm install внутри контейнера
make lint             # eslint
make typecheck        # tsc -b
make verify           # lint + typecheck
make format           # prettier
```

Если нужно добавить или удалить пакет:

```bash
make npm-add PACK=<package-name>
make npm-remove PACK=<package-name>
```

## Dev-среда

Основной сервис: `crm-app-wowbanner`  
Контейнер: `crm-frontend-wowbanner`

Порты:

- контейнер: `5173`
- хост: `5174`

## Nginx Proxy Manager

Контейнер работает в сети `proxy`. Чтобы открыть через домен:

- **Domain**: `crm.wowbanner.local`
- **Forward Hostname**: `crm-frontend-wowbanner`
- **Forward Port**: `5174` (dev) / `80` (prod)

---

# Архитектура проекта

Ниже идёт уже не базовая инструкция запуска, а актуальное описание архитектуры и правил развития проекта. Это секция нужна, чтобы после паузы можно было быстро восстановить контекст и продолжить улучшения.

## Текущее состояние

- архитектура выровнена под слои `app / pages / widgets / features / entities / shared`
- `lint` и `typecheck` проходят через контейнер
- CRUD-экраны для `clients`, `materials`, `processings`, `printings` приведены к одной базовой структуре
- общие modal-примитивы и API response-типы вынесены в `shared`

## Архитектурная карта `src/`

```text
src/
  app/
    providers/   # Query/Auth bootstrap
    router/      # маршрутизация приложения

  pages/
    clients/
    materials/
    processings/
    printings/
    home/
    not-found/

  widgets/
    app-header/
    app-sidebar/

  features/
    clients/
    materials/
    processings/
    printings/
    user/

  entities/
    client/
    material/
    processing/
    printing/
    user/
    session/

  shared/
    api/
    config/
    routing/
    lib/
    utils/
    ui/
    styles/
```

## Назначение слоёв

### `app`

Инициализация приложения:

- глобальные провайдеры
- auth bootstrap
- роутер
- верхний layout

### `pages`

Сборка экранов из готовых блоков.

Page-компонент должен быть максимально тонким:

- брать page-state из `model`
- получать данные из `entities`
- собирать экран из `features/widgets`

### `widgets`

Крупные стабильные части интерфейса, которые не выражают отдельное действие пользователя.

Примеры:

- шапка приложения
- сайдбар

### `features`

Пользовательские сценарии:

- формы создания/редактирования
- delete modals
- таблицы и toolbar-блоки, если они завязаны на сценарий работы

### `entities`

Доменная модель и работа с backend:

- `api/*.api.ts` — HTTP слой
- `model/types.ts` — доменные типы
- `model/query-keys.ts` — ключи React Query
- `model/use...` — query/mutation hooks

### `shared`

Общее, переиспользуемое и не привязанное к конкретной сущности:

- env/config
- endpoints
- общие response-типы
- базовые UI-компоненты
- общие helpers

## Правила импортов

Нужно придерживаться такой иерархии:

- `app` может импортировать всё ниже
- `pages` могут импортировать `widgets`, `features`, `entities`, `shared`
- `widgets` могут импортировать `features`, `entities`, `shared`
- `features` могут импортировать `entities`, `shared`
- `entities` должны опираться на `shared`; зависимости на другие `entities` допустимы только через публичный API и только если это реально доменная связь
- `shared` не импортирует слои выше

Часть ограничений зафиксирована в `eslint.config.js`.

## Внутренняя структура слайсов

Базовый шаблон:

```text
slice/
  api/
  model/
  lib/
  ui/
  index.ts
```

Не каждый каталог обязателен, но смысл такой:

- `api/` — HTTP, DTO, transport-layer типы
- `model/` — hooks, query keys, состояние, доменные типы
- `lib/` — мапперы, схемы, helpers внутри слайса
- `ui/` — React-компоненты
- `index.ts` — публичный API слайса

## Публичные API модулей

Импортировать снаружи нужно по возможности через `index.ts`, а не через внутренние файлы.

Хорошо:

```ts
import { useClientsQuery } from '@/entities/client';
import { ClientsPage } from '@/pages/clients';
```

Плохо:

```ts
import { useClientsQuery } from '@/entities/client/model/useClientsQuery';
```

Глубокие импорты допустимы только внутри самого слайса.

## Что уже вынесено в `shared`

### `shared/api`

- `client.ts` — общий axios client
- `endpoints.ts` — все backend endpoints
- `types.ts` — общие backend response-типы

Текущие общие типы:

- `ApiDataResponse<T>`
- `PaginatedResponse<T>`
- `ApiMutationResponse<T>`

### `shared/ui`

Базовые общие примитивы:

- `ModalDialog`
- `ConfirmActionModal`
- `FormErrorBanner`
- `FormModalFooter`
- `TableStateRow`
- `RowActionButtons`
- `AlertBanner`
- `PaginationBar`
- `RichTextEditor`
- `SearchField`
- `PhoneInputRu`

### `shared/lib`

Сейчас там лежат:

- `useCrudListPageState`
- `useModalFormState`
- `htmlToPlainText`
- `ruMobilePhone`

Правило:

- `shared/lib` — это reusable hooks и helpers, у которых есть прикладной UI/domain-смысл
- сюда попадают преобразования и механики, которые напрямую обслуживают формы, таблицы, modal-flow, поиск, отображение и пользовательский ввод
- если helper выражает повторяемый UI/domain pattern, его место обычно в `shared/lib`

### `shared/utils`

Сейчас там лежат:

- `getApiErrorMessage`
- JWT helpers

Правило:

- `shared/utils` — это маленькие технические pure-функции без UI-композиции и без локального состояния
- сюда попадают low-level helpers, которые не являются готовым UI/domain pattern
- если helper больше похож на инфраструктурную или техническую утилиту, чем на reusable UI primitive, его место в `shared/utils`

Текущее решение по проекту:

- переносов между `shared/lib` и `shared/utils` сейчас не требуется
- текущее распределение считается валидным и должно использоваться как опорное
- новые shared-файлы нужно добавлять только по этим правилам, а не “по ощущению”

## Текущие домены

### `entities/session`

Хранит и обслуживает сессию:

- refresh
- logout
- access token
- user session store

Это отдельный слой от `entities/user`.  
`user` — это сущность пользователя, `session` — это авторизация и текущая сессия.

### `entities/client`

- клиент
- типы клиента
- типы документов
- типы телефонов

### `entities/material`

- материалы
- изображения материалов
- alt-подписи изображений

### `entities/processing`

- обработки
- типы расчётов
- изображения обработок

### `entities/printing`

- типы печати

## Уже принятые паттерны

### 1. CRUD page state выносится из page-компонента

Примеры:

- `pages/clients/model/useClientsPage.ts`
- `pages/materials/model/useMaterialsPage.ts`
- `pages/processings/model/useProcessingsPage.ts`
- `pages/printings/model/usePrintingsPage.ts`

Страница должна быть максимально тонкой.

### 2. Delete modal строится через общий confirm primitive

Использовать:

- `ConfirmActionModal`

Не копировать руками layout, warning icon, footer и error block в каждом delete modal.

### 3. Ошибки формы показываются через общий компонент

Использовать:

- `FormErrorBanner`

### 4. Footer формы не копируется руками

Использовать:

- `FormModalFooter`

### 5. Локальная механика modal-форм

Для `submitError` и `handleClose` использовать:

- `useModalFormState`

Если при закрытии нужно очистить доп. состояние, передавать `onReset`.

### 6. Query keys не писать строками

Использовать фабрики:

- `clientKeys`
- `materialKeys`
- `processingKeys`
- `printingKeys`

### 7. Табличные shared-примитивы

Для таблиц уже приняты такие общие компоненты:

- `TableStateRow` — для empty/error состояний
- `RowActionButtons` — для action-кнопок в строках

Если кто-то меняет или создаёт новую таблицу, сначала нужно использовать эти shared-примитивы, а не копировать заново:

- пустое состояние
- error состояние
- кнопки `Редактировать / Удалить`

## Как добавлять новый экран

Шаблон:

```text
src/pages/orders/
  model/
    useOrdersPage.ts
  ui/
    OrdersPage.tsx
  index.ts
```

Порядок:

1. создать `entities/order`
2. создать `features/orders` или набор фич по необходимости
3. создать `pages/orders`
4. подключить маршрут в `app/router/AppRouter.tsx`
5. добавить пункт в sidebar navigation при необходимости

## Как добавлять новую сущность

Шаблон:

```text
src/entities/order/
  api/
    order.api.ts
  model/
    types.ts
    query-keys.ts
    useOrdersQuery.ts
    useOrderQuery.ts
    useCreateOrderCommand.ts
    useUpdateOrderCommand.ts
    useDeleteOrderCommand.ts
  index.ts
```

Рекомендации:

1. transport contract и HTTP слой держать в `api`
2. доменные типы и hooks держать в `model`
3. query keys добавлять сразу, не потом
4. наружу экспортировать только нужное через `index.ts`

## Как добавлять новую modal-форму

Рекомендуемый подход:

1. schema и default values в `lib/*.schema.ts`
2. мапперы request/view-model в `lib/*Mappers.ts`
3. UI-поля формы в `ui/`
4. `submitError` и `handleClose` через `useModalFormState`
5. error block через `FormErrorBanner`
6. footer через `FormModalFooter`

Если форма работает с изображениями или другим локальным сложным состоянием:

- локальное состояние хранить в самой форме
- очистку прокидывать в `useModalFormState({ onReset })`

## Как добавлять новый delete modal

Использовать `ConfirmActionModal`.

Внутри delete modal должно остаться только:

- локальное error state
- вызов mutation
- описание удаляемого объекта
- `handleClose`

Весь layout уже есть в shared.

## Где что искать

### Роуты

- `src/shared/routing/routes.ts`
- `src/app/router/AppRouter.tsx`

### Env

- `src/shared/config/env.ts`

### API endpoints

- `src/shared/api/endpoints.ts`

### HTTP client

- `src/shared/api/client.ts`

### Сессия

- `src/entities/session`

### Sidebar navigation

- `src/widgets/app-sidebar/model/navigation.ts`

## Проверка качества

Перед завершением изменений запускать:

```bash
make verify
```

На текущий момент команда должна проходить.

Если не проходит:

1. сначала исправить `lint`
2. потом `typecheck`
3. только потом двигаться дальше

## Текущий backlog улучшений

### Высокий приоритет

1. Развести `shared/lib` и `shared/utils` по чёткому правилу.
2. Проверить, что все feature-слайсы приведены к одинаковому внутреннему шаблону.
3. Если появятся ещё похожие сценарии, аккуратно продолжить вынос повторяющейся form-механики.

### Средний приоритет

4. Добавить smoke/e2e тесты на основные CRUD-потоки.
5. При необходимости вынести архитектурную документацию в `docs/architecture.md`, если README станет слишком большим.
6. При росте проекта дробить крупные feature-слайсы на более узкие по действию.

### Низкий приоритет

7. Пересмотреть naming внутри `shared` и `features`, если появятся новые домены и станет тесно.
8. Привести подписи UI и локальные комментарии к единому стилю.

## Как продолжать работу после паузы

Если нужно быстро восстановить контекст:

1. прочитать этот `README`
2. посмотреть `make help`
3. запустить `make verify`
4. посмотреть текущую структуру `src/`
5. выбрать следующий пункт из backlog выше

Рабочая последовательность для любого нового улучшения:

1. изучить код конкретного участка
2. понять, нет ли уже готового shared-примитива
3. сделать минимальное улучшение без лишней абстракции
4. прогнать `make verify`
5. обновить `README`, если появился новый паттерн или правило

## Важное правило

Не добавлять новую абстракцию “на всякий случай”.

Новый shared helper или shared component появляется только если:

- дублирование уже реально есть минимум в 2-3 местах
- abstraction делает код короче и понятнее
- она не прячет важную бизнес-логику

Для CRM это особенно важно: здесь лучше предсказуемая и скучная структура, чем “умная” система с лишней магией.
