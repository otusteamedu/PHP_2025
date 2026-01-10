### Самые прибыльные фильмы за неделю

**SQL-запрос:**
```sql
SELECT F.title, SUM(T.price) as profit 
    FROM films F
    JOIN session S ON F.id = S.film_id
    JOIN tickets T ON S.id = T.session_id
    JOIN orders ORD ON ORD.id = T.order_id
WHERE
  ORD.status = 'paid'
  AND ORD.created_at >= CURRENT_DATE - INTERVAL '7 days'
  AND ORD.created_at < CURRENT_DATE + INTERVAL '1 day'
GROUP BY
      F.id
ORDER BY
      profit DESC
      LIMIT 3;
```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по статусу заказа
CREATE INDEX IF NOT EXISTS "idx-orders-status" ON orders (status);

-- Для ускорения фильтрации по дате оплаты заказа
CREATE INDEX IF NOT EXISTS "idx-orders-created_at" ON orders (created_at);

-- Для ускорения агрегации по стоимости
CREATE INDEX IF NOT EXISTS "idx-tickets-price" ON tickets (price);

-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-film_id" ON sessions (film_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-session_id" ON tickets (session_id);
```


|                    | 10k            | 10kk without indexes | 10kk with indexes    | Result                 |
|--------------------|:---------------|:---------------------|:---------------------|:-----------------------|
| **Cost**           | 580.71..580.71 | 328803.38..328803.39 | 300585.04..300585.05 | Выигрыш в **1.1** раз  |
| **Actual time**    | 2.809..2.813   | 1664.656..1673.730   | 1094.308..1103.447   |                        |
| **Execution Time** | 2.859 ms       | 1674.966 ms          | 1104.543 ms          | Выигрыш в **1.5** раз  |