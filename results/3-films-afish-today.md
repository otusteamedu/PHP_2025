### Афиша фильмов на сегодня

**SQL-запрос:**
```sql
SELECT F.title, S.start_time
FROM
    films F
        JOIN session S ON F.id = S.film_id
WHERE
    S.start_time >= CURRENT_DATE
  AND S.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY
    S.start_time;

```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по дате начала сеанса
CREATE INDEX IF NOT EXISTS "idx-sessions-started_at_date" ON session (DATE(start_time));

-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-film_id" ON sessions (film_id);
```


|                    | 10k            | 10kk without indexes | 10kk with indexes  | Result                  |
|--------------------|:---------------|:---------------------|:-------------------|:------------------------|
| **Cost**           | 541.54..541.66 | 269049.75..273911.59 | 80531.95..81906.38 | Выигрыш в **3.34** раз  |
| **Actual time**    | 0.945..0.947   | 483.777..494.430     | 121.961..132.661   |                         |
| **Execution Time** | 0.967 ms       | 495.362 ms           | 133.076 ms         | Выигрыш в **3.72** раза |
