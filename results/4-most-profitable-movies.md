### Поиск 3 самых прибыльных фильмов за неделю

**SQL-запрос:**
```sql
SELECT
    m.name,
    SUM(t.price) as profit
FROM
    movies m
        JOIN sessions s ON m.id = s.movie_id
        JOIN tickets t ON s.id = t.session_id
        JOIN orders o ON o.id = t.order_id
WHERE
    o.status = 'paid'
    AND DATE(o.paid_at) >= CURRENT_DATE - INTERVAL '1 week' AND DATE(o.paid_at) < CURRENT_DATE
GROUP BY
    m.id
ORDER BY
    profit DESC
LIMIT 3;
```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по статусу заказа
CREATE INDEX IF NOT EXISTS "idx-orders-status" ON orders (status);

-- Для ускорения фильтрации по дате оплаты заказа
CREATE INDEX IF NOT EXISTS "idx-orders-paid_at_date" ON orders (DATE(paid_at));

-- Для ускорения агрегации по стоимости
CREATE INDEX IF NOT EXISTS "idx-tickets-price" ON tickets (price);

-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-movie_id" ON sessions (movie_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-session_id" ON tickets (session_id);
```


|                    | 10k            | 10kk without indexes | 10kk with indexes  | Result                  |
|--------------------|:---------------|:---------------------|:-------------------|:------------------------|
| **Cost**           | 577.28..577.29 | 163331.51..163331.52 | 75783.13..75783.14 | Выигрыш в **2.16** раз  |
| **Actual time**    | 2.850..2.853   | 1318.675..1328.685   | 742.394..751.801   |                         |
| **Execution Time** | 2.904 ms       | 1328.938 ms          | 752.116 ms         | Выигрыш в **1.77** раза |