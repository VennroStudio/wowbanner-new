# Промпт: Создание новой сущности

````
Изучи правила проекта в docs/agent-rules.md.

## Задача

Создай новую сущность `Article` в модуле `Blog`.

## Описание сущности

Статья блога. Пользователи-администраторы могут создавать, редактировать 
и удалять статьи. Обычные пользователи могут только читать.

## Поля сущности

| Поле | Тип | Обязательное | Описание |
|---|---|---|---|
| id | int (auto) | да | Первичный ключ |
| title | string (255) | да | Заголовок статьи |
| slug | string (255) | да | URL-slug, уникальный |
| content | text | да | Содержимое статьи |
| status | ArticleStatus (enum) | да | Статус публикации |
| authorId | int | да | ID автора (User) |
| createdAt | DateTimeImmutable | да | Дата создания |
| updatedAt | DateTimeImmutable | нет | Дата обновления |
| deletedAt | DateTimeImmutable | нет | Soft delete |

## Enum-поля

### ArticleStatus
| Значение | ID | Описание |
|---|---|---|
| DRAFT | 1 | Черновик |
| PUBLISHED | 2 | Опубликована |
| ARCHIVED | 3 | В архиве |

## Методы API

### 1. Создание
- Метод: `POST`
- URL: `/v1/blog/article/create`
- Защищённый: да (только admin)
- Поля: title (2-255), slug (2-255, regex: [a-z0-9-]+), content (не пустой), status (enum)
- Ответ: `201 { "data": { "success": 1 } }`

### 2. Получение по ID
- Метод: `GET`
- URL: `/v1/blog/article/{id}`
- Защищённый: нет
- Ответ: `200 { "data": { "id": 1, "title": "...", "slug": "...", "content": "...", "status": { "id": 2, "label": "Опубликована" }, "author_id": 1, "created_at": "...", "updated_at": null } }`

### 3. Список
- Метод: `GET`
- URL: `/v1/blog/article`
- Защищённый: нет
- Фильтры: search (по title), status (enum), dateFrom, dateTo
- Пагинация: page, perPage
- Ответ: `200 { "data": { "count": N, "items": [...] } }`

### 4. Обновление
- Метод: `PATCH`
- URL: `/v1/blog/article/update/{id}`
- Защищённый: да (только admin)
- Поля: title, slug, content, status
- Ответ: `200 { "data": { "success": 1 } }`

### 5. Удаление (soft delete)
- Метод: `DELETE`
- URL: `/v1/blog/article/delete/{id}`
- Защищённый: да (только admin)
- Ответ: `200 { "data": { "success": 1 } }`

## Бизнес-правила

- slug должен быть уникальным
- Удалённую статью нельзя редактировать
- Только admin может создавать/редактировать/удалять

## Связи с другими сущностями

- authorId ссылается на User, но связь через ID (не через ORM relation)

## Что создать

Следуя docs/agent-rules.md, создай **все компоненты** в правильном порядке:

1. **Entity** — Article + ArticleStatus enum
2. **Repository** — ArticleRepository + DoctrineArticleRepository
3. **Command/Handler** — CreateArticle, UpdateArticle, DeleteArticle
4. **Query/Fetcher** — ArticleGetById, ArticleFindAll, ArticleFindBySlug
5. **ReadModel** — ArticleById, ArticleListItem, ArticleBySlug + ArticleViewInterface
6. **Action** — Create, GetById, GetList, Update, Delete
7. **Translation** — errors + validators (en + ru)
8. **Маршруты** — добавить в config/routes/v1.php
9. **Frontend docs** — docs/frontend/Blog.md

Миграции НЕ создавать, я сделаю их сам.
````
