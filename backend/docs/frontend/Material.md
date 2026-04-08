# Фронт: Material (материалы)

Справочник материалов: CRUD для админки и управление изображениями. Все эндпоинты защищены JWT; операции разрешены роли **администратор** (см. бизнес-правила на бэкенде).

---

## Базовые вещи

- **Base URL:** `{BACKEND_BASE_URL}/v1`
- **Content-Type:** `application/json` для запросов с телом (кроме загрузки файлов)
- **Защита:** заголовок `Authorization: Bearer <access_token>`
- **Успех с данными:** `{ "data": { ... } }`
- **Ошибка домена:** `{ "error": { "code": number, "message": string } }` (часто `409`)
- **Ошибка валидации (422):** `{ "validations": [{ "field": string, "message": string }] }`
- **401** — невалидный/отсутствующий JWT
- **403** — нет прав на операцию

---

## 1. Список материалов (GetMaterialsAction)

**Цель:** пагинированный список с поиском по названию. Содержит список привязанных изображений.

**Запрос:**
- Метод: `GET`
- URL: `/v1/materials`
- Заголовок: `Authorization: Bearer <access_token>`

**Query-параметры:**
- `page` — номер страницы (default: `1`)
- `perPage` — размер страницы (default: `20`)
- `search` — подстрока в поле `name` (опционально)

**Успех (200):**
```json
{
  "data": {
    "count": 42,
    "items": [
      {
        "id": 1,
        "name": "Баннер",
        "description": "Описание материала",
        "images": [
          {
            "id": 1,
            "path": "https://s3.example.com/material/uuid.jpg",
            "alt": "Описание фото"
          }
        ]
      }
    ]
  }
}
```

---

## 2. Материал по ID (GetMaterialByIdAction)

**Цель:** детальная информация о материале.

**Запрос:**
- Метод: `GET`
- URL: `/v1/materials/{id}`
- Заголовок: `Authorization: Bearer <access_token>`

**Успех (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Баннер",
    "description": "Описание материала",
    "images": [
      {
        "id": 1,
        "path": "https://s3.example.com/material/uuid.jpg",
        "alt": "Описание фото"
      }
    ]
  }
}
```

---

## 3. Создание материала (CreateMaterialAction)

**Цель:** создать новую запись в справочнике.

**Запрос:**
- Метод: `POST`
- URL: `/v1/materials/create`
- Заголовок: `Authorization: Bearer <access_token>`

```json
{
  "name": "Баннер",
  "description": "Описание"
}
```

**Успех (201):**
```json
{ "data": [] }
```

---

## 4. Обновление материала (UpdateMaterialAction)

**Цель:** изменить название и описание.

**Запрос:**
- Метод: `PATCH`
- URL: `/v1/materials/update/{id}`
- Заголовок: `Authorization: Bearer <access_token>`

```json
{
  "name": "Новое название",
  "description": "Новое описание"
}
```

**Успех (200):**
```json
{ "data": [] }
```

---

## 5. Удаление материала (DeleteMaterialAction)

**Цель:** удалить запись из справочника.

**Запрос:**
- Метод: `DELETE`
- URL: `/v1/materials/delete/{id}`
- Заголовок: `Authorization: Bearer <access_token>`

**Успех (200):**
```json
{ "data": [] }
```

---

## 6. Добавление изображений (CreateMaterialImageAction)

**Цель:** загрузить одну или несколько фотографий для материала.

**Запрос:**
- Метод: `POST`
- URL: `/v1/materials/{id}/images`
- Заголовок: `Authorization: Bearer <access_token>`
- Content-Type: `multipart/form-data`

| Поле | Тип | Описание |
| :--- | :--- | :--- |
| `images[]` | File (binary) | Массив файлов изображений |
| `imageAlts[]` | String | Массив альтернативных текстов (в том же порядке) |

**Успех (200):**
```json
{ "data": [] }
```

---

## 7. Обновление описания изображения (UpdateMaterialImageAction)

**Цель:** изменить альт-текст загруженного изображения.

**Запрос:**
- Метод: `PATCH`
- URL: `/v1/materials/images/{imageId}`
- Заголовок: `Authorization: Bearer <access_token>`

```json
{
  "alt": "Новое описание фото"
}
```

**Успех (200):**
```json
{ "data": [] }
```

---

## 8. Удаление изображения (DeleteMaterialImageAction)

**Цель:** удалить конкретную фотографию.

**Запрос:**
- Метод: `DELETE`
- URL: `/v1/materials/images/{imageId}`
- Заголовок: `Authorization: Bearer <access_token>`

**Успех (200):**
```json
{ "data": [] }
```
