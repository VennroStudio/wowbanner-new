# Agent Rules — CRM Frontend (`crm-frontend`)

Справочник по слоям, соглашениям и паттернам на примере сущности **Client**. Правила — ориентир: у каждой фичи свои поля, валидация и UX.

---

## Разделы

1. [Структура слоёв (FSD-лайт)](#1-структура-слоёв-fsd-лайт)
2. [Entity: типы, API, React Query](#2-entity-типы-api-react-query)
3. [Фича: UI, форма, маппинг](#3-фича-ui-форма-маппинг)
4. [Страницы: композиция и состояние](#4-страницы-композиция-и-состояние)
5. [Shared: API, UI, утилиты](#5-shared-api-ui-утилиты)
6. [Исключения и ошибки API](#6-исключения-и-ошибки-api)

---

## 1. Структура слоёв (FSD-лайт)

| Слой | Назначение | Пример (Client) |
|------|------------|-------------------|
| **`entities/<name>/`** | Доменная модель, типы, вызовы API, хуки данных (`useQuery` / `useMutation`) без UI | `entities/client/` |
| **`features/<feature>/`** | Сценарии и экранные блоки: таблицы, модалки, формы, специфичные для продукта компоненты | `features/clients/` |
| **`pages/`** | Сборка страниц: состояние экрана, связывание фич и сущностей, роутинг | `pages/ClientsPage/` |
| **`shared/`** | Переиспользуемое: `api/client`, константы, UI-кит, общие утилиты | `shared/api`, `shared/ui` |

**Импорты:** слой выше может импортировать ниже (`pages` → `features` → `entities` → `shared`). Не тянуть `entities` из `shared` и не тянуть `features` в `entities`.

---

## 2. Entity: типы, API, React Query

### 2.1 Типы

- **`model/types.ts`** — типы ответа API (как приходит с бэкенда: `snake_case` полей).
- Типы DTO для **тела запроса** (create/update) допускают в `api/api.ts` рядом с `clientApi`, если они не нужны как модель «сущности».

### 2.2 API

- **`api/api.ts`** — объект `*Api` с методами `getX`, `createX`, `updateX`, `deleteX`.
- Базовый URL и пути — из **`shared/constants`** (`API_ENDPOINTS`), не хардкодить строки в entity.
- Транспорт — **`apiClient`** из `shared/api/client` (axios).

### 2.3 Хуки

| Паттерн | Имя | Назначение |
|---------|-----|------------|
| Список / фильтры | `useClientsQuery` | `useQuery`, ключ `['clients', { page, perPage, search }]` — **примитивы в объекте**, без «сырых» объектов параметров с каждого рендера без нормализации |
| Одиночная сущность | `useClientQuery` | `useQuery`, ключ `['client', id]`; `enabled` при `id > 0` и открытой модалке |
| Создание | `useCreateClientCommand` | `useMutation` + `invalidateQueries` на `['clients']` |
| Обновление | `useUpdateClientCommand` | `invalidateQueries` на `['clients']` и `['client', id]` |
| Удаление | `useDeleteClientCommand` | `invalidateQueries` на `['clients']` и `['client', id]` |

- Для списков при пагинации уместно **`placeholderData: (prev) => prev)`** — уменьшить мигание при смене страницы.
- **`queryKey`** согласовывать с инвалидациями: префикс `['clients']` инвалидирует все списки.

### 2.4 Публичный API entity

- **`entities/<name>/index.ts`** реэкспортирует только то, что нужно страницам и фичам: типы, `*Api`, хуки.

---

## 3. Фича: UI, форма, маппинг

### 3.1 Компоненты

- Размещать в **`features/<feature>/components/`** по смыслу (таблица, модалка, шапка).
- Переиспользуемые примитивы (поиск, пагинация, баннер, модалка-обёртка) — в **`shared/ui`**, не дублировать в фиче.

### 3.2 Форма (пример Client)

- **`lib/clientFormSchema.ts`** — схема Zod + `defaultValues` для `react-hook-form`.
- **`lib/clientFormMappers.ts`**:
  - `mapApiToForm` / `mapClientToFormValues` — из ответа API в значения формы;
  - `buildCreateBody` / `buildUpdateBody` — из формы в контракт API (`camelCase` в теле, как ожидает бэкенд).
- **`ClientFormModal.tsx`** — оркестрация: хуки entity, `useForm`, `reset` при смене режима/данных, `mutateAsync`, показ ошибок через `getApiErrorMessage`.

**Согласование с бэкендом:** при обновлении клиента полный список телефонов/компаний передаётся в синкер; для «снятия» типа юрлица передача `companies: []` соответствует удалению компаний на сервере.

### 3.3 Колбэки успеха

- Колбэки вроде **`onSuccess?: (mode: 'create' \| 'edit') => void`** передавать из модалки, чтобы текст уведомлений на странице не зависел от внешнего `state` в момент закрытия (избегаем путаницы с «создан / сохранён»).

---

## 4. Страницы: композиция и состояние

- **`pages/<Name>Page/`** держит локальный UI-state: поиск, страница пагинации, открытые модалки, тосты/баннеры.
- **Дебаунс поиска** — на странице (`useEffect` + `setTimeout`), сброс **`page`** на `1` при изменении строки поиска.
- Данные списка — **`useClientsQuery({ search: debounced, page, perPage })`** из entity.

---

## 5. Shared: API, UI, утилиты

- **`shared/api/client.ts`** — axios instance, interceptors (например 401 → редирект на auth).
- **`shared/constants`** — `API_URL`, `ROUTES`, `API_ENDPOINTS`.
- **`shared/ui`** — дизайн-система лайт: `ModalDialog`, `SearchField`, `PaginationBar`, `AlertBanner`, классы полей формы (`form/fieldClasses`).
- **`shared/utils`** — разбор ошибок (`axiosError`).

---

## 6. Исключения и ошибки API

- Пользователю показывать **сообщение ответа** через `getApiErrorMessage(error)`; в формах — отдельный блок под полями или над кнопками.
- Глобальный **401** обрабатывается в interceptor; не дублировать в каждом запросе.

---

## Чеклист новой сущности (по аналогии с Client)

1. `entities/<name>/model/types.ts` + `api/api.ts` + хуки query/list + mutations.
2. Ключи React Query и инвалидации согласованы.
3. Фича: таблица/формы/модалки, Zod + mappers, без дублирования `shared/ui`.
4. Страница: только композиция и локальный state.
5. Эндпоинты в `API_ENDPOINTS`; при появлении нового модуля на бэкенде — дописать **`docs/frontend/{Module}.md`** в репозитории бэкенда (см. `backend/docs/rules/frontend-api-docs.md`).
