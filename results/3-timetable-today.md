### Формирование афиши (фильмы, которые показывают сегодня)

**SQL-запрос:**
```sql
SELECT
    m.*,
    s.started_at
FROM
    movies m
        JOIN sessions s ON m.id = s.movie_id
WHERE
    DATE(s.started_at) = CURRENT_DATE
ORDER BY
    s.started_at;
```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по дате начала сеанса
CREATE INDEX IF NOT EXISTS "idx-sessions-started_at_date" ON sessions (DATE(started_at));

-- Для ускорения сортировки по дате начала сеанса
CREATE INDEX IF NOT EXISTS "idx-sessions-started_at" ON sessions (started_at);

-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-movie_id" ON sessions (movie_id);
```


|                    | 10k            | 10kk without indexes | 10kk with indexes  | Result                  |
|--------------------|:---------------|:---------------------|:-------------------|:------------------------|
| **Cost**           | 535.54..535.66 | 130640.96..133071.76 | 94174.09..96604.89 | Выигрыш в **1.38** раз  |
| **Actual time**    | 1.512..1.513   | 420.610..432.143     | 152.323..162.316   |                         |
| **Execution Time** | 1.532 ms       | 432.490 ms           | 162.697 ms         | Выигрыш в **2.67** раза |