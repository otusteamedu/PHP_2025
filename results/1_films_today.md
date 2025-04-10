### Выбор фильмов на сегодня

**SQL-запрос:**
```sql
SELECT f.title FROM films F
    JOIN session S ON F.id = S.film_id
        WHERE S.start_time >= CURRENT_DATE
        AND S.start_time < CURRENT_DATE + INTERVAL '1 day';
```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по дате начала сеанса
CREATE INDEX IF NOT EXISTS "idx-sessions-start_time" ON session (start_time);

-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-film_id" ON session (film_id);
```


|                    | 10k          | 10kk without indexes | 10kk with indexes | Result                  |
|--------------------|:-------------|:---------------------|:------------------|:------------------------|
| **Cost**           | 0.00..249.00 | 1000.43..213148.16   | 1197.77..81576.69 | Выигрыш в **2.61** раз  |
| **Actual time**    | 0.106..1.075 | 4.902..511.296       | 4.378..134.736    |                         |
| **Execution Time** | 1.113 ms     | 512.721 ms           | 135.317 ms        | Выигрыш в **3.79** раз |

