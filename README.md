# Rick and Morty API Test (Laravel 12)

Сервис на Laravel 12 (PHP 8.3 + MySQL в Docker), который:
- импортирует персонажей и эпизоды из Rick and Morty API,
- при первом импорте загружает к каждому эпизоду 50-500 отзывов из JSON,
- позволяет добавлять отзывы через API,
- отдаёт список эпизодов с отзывами и персонажами с фильтрами/сортировкой/пагинацией.

## Стек

- Laravel 12
- PHP 8.3
- MySQL 8.4
- Docker Compose

## Запуск

```bash
docker compose up -d --build
```

```bash
docker compose run --rm app php artisan migrate
```

```bash
docker compose run --rm app php artisan rickandmorty:sync
```

```bash
docker compose up -d app
```

API будет доступен на `http://localhost:8000`.

## Переменные окружения

Ключевые настройки в `.env`:

- `REVIEW_RATING_STRATEGY=random|sentiment`
- `RICKMORTY_BASE_URL=https://rickandmortyapi.com/api`
- `REVIEWS_JSON_PATH=reviews.json`
- `REVIEWS_SEED_MIN=50`
- `REVIEWS_SEED_MAX=500`

## Планировщик

Синхронизация данных запускается по расписанию раз в час:

- команда: `rickandmorty:sync`
- настройка: `routes/console.php`

Для cron (пример):

```bash
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

## API

### 1) Получить эпизоды (с отзывами и персонажами)

`GET /api/episodes`

Поддерживаемые query-параметры:
- `character_ids` - CSV external_id персонажей, например `1,2,3`
- `character` - фильтр по имени персонажа (LIKE)
- `season` - номер сезона
- `air_date_from` - дата выхода от (`YYYY-MM-DD`)
- `air_date_to` - дата выхода до (`YYYY-MM-DD`)
- `sort_by` - `air_date` или `avg_rating`
- `sort_dir` - `asc` или `desc`
- `per_page` - размер страницы (1..100)
- `page` - номер страницы

Пример:

```bash
curl "http://localhost:8000/api/episodes?season=1&character_ids=1,2&sort_by=avg_rating&sort_dir=desc&per_page=5"
```

### 2) Добавить отзыв к эпизоду

`POST /api/episodes/{episode}/reviews`

Тело:

```json
{
  "author": "John Doe",
  "text": "Очень понравилась серия"
}
```

Пример:

```bash
curl -X POST "http://localhost:8000/api/episodes/1/reviews" \
  -H "Content-Type: application/json" \
  -d '{"author":"John Doe","text":"Очень понравилась серия"}'
```

Рейтинг выставляется автоматически в диапазоне `1.0..5.0` согласно `REVIEW_RATING_STRATEGY`.

## Тесты

```bash
docker compose run --rm app php artisan test
```
