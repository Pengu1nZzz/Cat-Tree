# CatTree — Сервис учета кошек и генеалогических связей 🐾

Сервис для ведения базы данных кошек с учетом их родословной, поддержкой генеалогических связей (мать и множество возможных отцов), фильтрацией и веб-интерфейсом.

Проект разработан в парадигме **Clean Architecture / Use-Case Driven Architecture** в соответствии со стандартами enterprise-разработки на базе Laravel.

---

## 📋 Основной функционал

- **Учет кошек:** кличка, пол (`male` / `female`), возраст (в годах).
- **CRUD операции:** просмотр, добавление, редактирование и удаление записей.
- **Генеалогические связи:**
  - Указание биологической матери котенка (только из зарегистрированных самок).
  - Указание множества возможных отцов (из-за биологических особенностей кошачьих, только из зарегистрированных самцов).
  - Просмотр потомства кошки (все котята, для которых особь является матерью или одним из возможных отцов).
- **Фильтрация и поиск:**
  - По полу (`male` / `female`).
  - По точному возрасту (`age`).
  - По диапазону возраста (`min_age`, `max_age`).
  - Поиск по кличке (`search`).
  - Сортировка по имени, возрасту, дате добавления.
- **Два интерфейса взаимодействия:**
  - Полнофункциональный адаптивный **Web UI** на Blade + Tailwind CSS.
  - Строго типизированный **REST API (v1)** с JSON-ответами.

---

## 🏛 Архитектура проекта

Проект построен по принципам чистой архитектуры с четким разделением ответственности слоев:

```
                  ┌───────────────────────────────┐
                  │   HTTP Requests / Web & API   │
                  └──────────────┬────────────────┘
                                 │
                                 ▼
                     ┌───────────────────────┐
                     │ Form Requests / DTOs  │
                     └───────────┬───────────┘
                                 │
                                 ▼
                     ┌───────────────────────┐
                     │    UseCaseFactory     │
                     └───────────┬───────────┘
                                 │
                                 ▼
                     ┌───────────────────────┐
                     │   Use Cases (Domain)  │
                     └───────────┬───────────┘
                                 │
                                 ▼
                     ┌───────────────────────┐
                     │ Repository Contracts  │
                     └───────────┬───────────┘
                                 │
                                 ▼
                     ┌───────────────────────┐
                     │ Repository Impl / DB  │
                     └───────────────────────┘
```

### Ключевые компоненты:

1. **Use Cases (`app/UseCases/Cat/`):**
   - Каждый Use Case инкапсулирует одну бизнес-операцию (`ListCatsUseCase`, `GetCatUseCase`, `CreateCatUseCase`, `UpdateCatUseCase`, `DeleteCatUseCase`).
   - Разрешаются через фабрику `UseCaseFactory` по enum `UseCaseSystemNamesEnum` и конфигу `config/use_cases.php`.
2. **DTO (Data Transfer Objects):**
   - Строгая типизация входных данных (`InputDTO`) и ответов (`CatDTO`, `PaginatedListDTO`).
3. **Repository Pattern (`app/Models/Cat/`):**
   - Контракт `ICatDatabaseRepository` определяет интерфейс доступа к данным.
   - Реализация `CatDatabaseRepository` инкапсулирует запросы Eloquent ORM и маппинг в DTO.
4. **Слой валидации (`app/Http/Requests/Cat/`):**
   - `StoreCatRequest` и `UpdateCatRequest` валидируют корректность данных, проверяют соответствие пола родителей (мать — самка, отцы — самцы) и исключают циклические ссылки (кошка не может быть родителем самой себе).

---

## 🛠 Стек технологий

- **PHP 8.2+** / **Laravel 12**
- **PostgreSQL 16** (основная СУБД)
- **Redis 7** (кэширование и сессии)
- **Nginx** (веб-сервер)
- **Docker & Docker Compose** (контейнеризация)
- **Tailwind CSS** (интерфейс Blade)
- **PHPUnit** (автоматизированное тестирование)

---

## 🚀 Быстрый старт (Docker)

### 1. Клонирование и настройка окружения

```bash
git clone <repository_url> CatTree
cd CatTree

cp .env.example .env
```

### 2. Запуск контейнеров

```bash
docker compose up -d --build
```

Сервисы будут доступны по следующим адресам и портам:
- **Web UI & REST API:** `http://localhost:31080`
- **PostgreSQL:** `localhost:15432` (пользователь `user`, пароль `password`, БД `cat_tree_db`)
- **Redis:** `localhost:16379`

### 3. Применение миграций

```bash
docker compose exec php-fpm php artisan migrate
```

*(Опционально)* Генерация ключа приложения:
```bash
docker compose exec php-fpm php artisan key:generate
```

---

## 🖥 Веб-интерфейс

В проекте реализован удобный интерфейс управления:

- **Список кошек и фильтры:** `http://localhost:31080/cats`
- **Добавление новой кошки:** `http://localhost:31080/cats/create`
- **Карточка кошки и родословная:** `http://localhost:31080/cats/{id}`
- **Редактирование данных:** `http://localhost:31080/cats/{id}/edit`

---

## 📡 REST API (v1)

Базовый префикс API: `/api/v1/cats`

### Эндпоинты

| Метод | URL | Описание |
|---|---|---|
| `GET` | `/api/v1/cats` | Список кошек с пагинацией и фильтрами |
| `GET` | `/api/v1/cats/{id}` | Получение кошки по ID со связями |
| `POST` | `/api/v1/cats` | Создание новой кошки |
| `PUT` | `/api/v1/cats/{id}` | Обновление информации о кошке |
| `DELETE` | `/api/v1/cats/{id}` | Удаление кошки |

### Параметры фильтрации (`GET /api/v1/cats`)

- `gender` — `male` или `female`
- `age` — точный возраст (целое число)
- `min_age` — минимальный возраст
- `max_age` — максимальный возраст
- `mother_id` — ID матери
- `father_id` — ID одного из возможных отцов
- `search` — поиск по подстроке в кличке
- `page` — номер страницы (по умолчанию `1`)
- `per_page` — количество записей на странице (по умолчанию `15`)
- `sort_by` — поле сортировки (`id`, `name`, `age`, `created_at`)
- `sort_direction` — направление (`asc`, `desc`)

### Примеры запросов

#### 1. Создание кошки с указанием матери и возможных отцов
```bash
curl -X POST http://localhost:31080/api/v1/cats \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Снежок",
    "gender": "male",
    "age": 1,
    "mother_id": 1,
    "possible_father_ids": [2, 3]
  }'
```

**Ответ (201 Created):**
```json
{
  "id": 4,
  "name": "Снежок",
  "gender": "male",
  "age": 1,
  "mother_id": 1,
  "mother": {
    "id": 1,
    "name": "Мурка",
    "gender": "female",
    "age": 3
  },
  "possible_fathers": [
    { "id": 2, "name": "Барсик", "gender": "male", "age": 4 },
    { "id": 3, "name": "Рыжик", "gender": "male", "age": 2 }
  ],
  "created_at": "2026-09-29T12:00:00.000000Z",
  "updated_at": "2026-09-29T12:00:00.000000Z"
}
```

#### 2. Фильтрация кошек
```bash
curl -X GET "http://localhost:31080/api/v1/cats?gender=female&min_age=2&max_age=5" \
  -H "Accept: application/json"
```

---

## 🧪 Тестирование

Проект покрыт полным набором модульных и функциональных тестов (Feature & Unit):
- Тестирование всех CRUD-операций REST API.
- Тестирование валидации (проверка пола родителей, циклов, граничных условий).
- Тестирование фильтрации и пагинации.
- Тестирование контроллеров и представлений веб-интерфейса Blade.

Для запуска тестов выполните:

```bash
docker compose exec php-fpm php artisan test
```

Или локально (при установленном окружении PHP/Composer):
```bash
php artisan test
```

---

## 📂 Структура каталогов

```
CatTree/
├── app/
│   ├── Enum/                  # Перечисления (GenderEnum, UseCaseSystemNamesEnum)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/        # REST API контроллеры
│   │   │   └── Web/           # Blade Web контроллеры
│   │   └── Requests/Cat/      # Form Requests с правилами валидации
│   ├── Models/
│   │   ├── Base/DTO/          # Базовые DTO (ModelDTO, ListDTO, PaginatedListDTO)
│   │   └── Cat/
│   │       ├── Contracts/     # Интерфейсы репозиториев (ICatDatabaseRepository)
│   │       ├── DTO/           # CatDTO
│   │       ├── Repositories/  # CatDatabaseRepository
│   │       └── Cat.php        # Eloquent-модель
│   └── UseCases/
│       ├── Base/              # Базовый абстрактный класс Use Case
│       ├── Cat/               # Use Cases домена Cat и их InputDTO
│       └── UseCaseFactory.php # Фабрика инстанцирования Use Cases
├── config/
│   └── use_cases.php          # Сопоставление системных имен и классов Use Cases
├── database/
│   └── migrations/            # Миграции таблиц cats и cat_fathers
├── deployment/local/          # Docker-конфигурации (Nginx, PHP-FPM)
├── resources/views/           # Blade-шаблоны веб-интерфейса
├── routes/
│   ├── api.php                # Маршруты REST API
│   └── web.php                # Маршруты веб-интерфейса
└── tests/                     # Unit и Feature тесты
```
