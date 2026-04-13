# crm-frontend-wowbanner

React + TypeScript фронтенд. Сборка через Vite, окружение через Docker.

## Структура проекта

```
auth-frontend/
├── docker/
│   ├── development/
│   │   └── Dockerfile        # dev-контейнер (Vite dev server)
│   └── production/
│       ├── Dockerfile        # prod-образ (сборка + nginx)
│       └── nginx.conf        # конфиг nginx для SPA
├── src/                      # исходный код
├── .env                      # переменные окружения (не коммитить)
├── .env.deploy               # переменные для деплоя (не коммитить)
├── .env.example              # шаблон .env
├── docker-compose.yml        # dev-окружение
├── docker-compose-production.yml  # prod-окружение (registry)
└── Makefile                  # команды управления проектом
```

## Первый запуск

### 1. Создать Vite-проект (если src/ ещё не существует)

```bash
make create
```

Добавить в `vite.config.ts`:
```ts
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'

export default defineConfig({
    plugins: [react(), tailwindcss()],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'src'),
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5174,
        allowedHosts: ['crm.wowbanner.local'],
    },
})
```

### 2. Поднять контейнер

```bash
make init
```

## Качество кода

```bash
make lint    # проверка ESLint
make format  # форматирование Prettier
```

---

## Nginx Proxy Manager

Контейнер работает в сети `proxy`. Чтобы открыть через домен:

- **Domain**: `crm.wowbanner.local`
- **Forward Hostname**: `crm-frontend-wowbanner`
- **Forward Port**: `5174` (dev) / `80` (prod)