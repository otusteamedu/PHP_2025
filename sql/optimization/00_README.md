# Оптимизация запросов к БД кинотеатра

## Структура

```
sql/optimization/
├── 00_README.md              ← этот файл
├── q1_films_today.sql        ← Q1: Какие фильмы идут сегодня
├── q2_tickets_weekly.sql     ← Q2: Сколько билетов продано за неделю
├── q3_today_poster.sql       ← Q3: Афиша кинотеатра на сегодня
├── q4_top3_revenue.sql       ← Q4: Топ-3 самых прибыльных фильмов
├── q5_seat_map.sql           ← Q5: Схема зала (свободные/занятые места)
└── q6_price_range.sql        ← Q6: Min/Max цена билета на сеанс
```

Каждый файл содержит полный цикл анализа одного запроса:

| Секция | Что внутри |
|--------|-----------|
| **Запрос** | SQL-текст запроса |
| **EXPLAIN до — 10K строк** | План на маленькой БД (DML_10000.sql) + анализ |
| **EXPLAIN до — 10M строк** | План на большой БД (DML_10000000.sql) + анализ |
| **Предлагаемые улучшения** | Индексы, которые нужно создать, с обоснованием |
| **EXPLAIN после — 10M строк** | План после применения оптимизаций (optimizations.sql) + анализ |

## Что было сделано (сводка индексов)

| Индекс | Для запроса | Тип | Размер на 10M | Эффект |
|--------|-------------|-----|---------------|--------|
| `idx_screenings_start_time_hall_movie` | Q1, Q3 | Покрывающий | малый (часть screenings) | Index Only Scan, heap fetches = 0 |
| `idx_tickets_sold_purchase_time` | Q2 | Частичный (status = 'sold') | ~64 MB | Только sold-строки, Index Only Scan |
| `idx_tickets_sold_screening_price` | Q4 | Частичный (status = 'sold') | ~265 MB | Index Only Join + SUM без обращения к таблице |
| `idx_tickets_screening_seat_occupied` | Q5 | Частичный (status IN 'sold','reserved') | ~204 MB | Index Only Scan для LEFT JOIN |
| `idx_tickets_screening_price` | Q6 | Покрывающий | ~301 MB | MIN/MAX по B-Tree за O(log N) |

## Про файл EXPLAIN_10000.sql

Изначально планы были сохранены одним коммитом в `EXPLAIN_10000.sql` и дублировались для 10M и 10M+оптимизированный в `EXPLAIN_10000000.sql` и `EXPLAIN_10000000_optimized.sql`. Это неудобно:
- Чтобы сравнить «было/стало» по одному запросу, надо открыть 3 файла
- Нет анализа: почему так получилось и что делать
- Нет связи запроса с его EXPLAIN и улучшениями

В новом формате всё в одном файле на запрос: запрос → EXPLAIN до → анализ → улучшения → EXPLAIN после.

## Как использовать

```bash
# 1. Создать БД и таблицы
psql -U postgres -d mydatabase -f films_DDL.sql

# 2. Загрузить тестовые данные
psql -U postgres -d mydatabase -f DML_10000000.sql

# 3. Применить оптимизации (создание индексов)
psql -U postgres -d mydatabase -f optimizations.sql

# 4. Посмотреть анализ по любому запросу
less sql/optimization/q1_films_today.sql
