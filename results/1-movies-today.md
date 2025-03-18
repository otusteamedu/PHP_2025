### Выбор всех фильмов на сегодня

**SQL-запрос:**
```sql
SELECT
    m.name
FROM
    movies m
        JOIN sessions s ON m.id = s.movie_id
WHERE
    DATE(s.started_at) = CURRENT_DATE;
```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по дате начала сеанса
CREATE INDEX IF NOT EXISTS "idx-sessions-started_at_date" ON sessions (DATE(started_at));

-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-movie_id" ON sessions (movie_id);
```


|                    | 10k          | 10kk without indexes | 10kk with indexes | Result                  |
|--------------------|:-------------|:---------------------|:------------------|:------------------------|
| **Cost**           | 0.29..534.13 | 1000.43..132445.77   | 1282.62..95978.91 | Выигрыш в **1.38** раз  |
| **Actual time**    | 0.100..1.319 | 1.104..450.693       | 2.963..163.165    |                         |
| **Execution Time** | 1.333 ms     | 451.085 ms           | 163.676 ms        | Выигрыш в **2.77** раза |