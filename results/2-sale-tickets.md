### Все покупки за неделю

**SQL-запрос:**
```sql
SELECT count(*) FROM tickets T
    JOIN orders ORD ON ORD.id = T.order_id
    WHERE
        ORD.status='paid'
        AND ORD.created_at >= CURRENT_DATE - INTERVAL '7 days'
        AND ORD.created_at < CURRENT_DATE + INTERVAL '1 day';
```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по статусу заказа
CREATE INDEX IF NOT EXISTS "idx-orders-status" ON orders (status);

-- Для ускорения фильтрации по дате оплаты заказа
CREATE INDEX IF NOT EXISTS "idx-orders-created_at" ON orders (created_at);

-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id); 
```


|                    | 10k            | 10kk without indexes | 10kk with indexes    | Result                  |
|--------------------|:---------------|:---------------------|:---------------------|:------------------------|
| **Cost**           | 562.62..562.63 | 304006.48..304006.49 | 121813.73..121813.74 | Выигрыш в **2.49** раз  |
| **Actual time**    | 2.420..2.423   | 1184.804..1192.281   | 392.062..400.502     |                         |
| **Execution Time** | 2.457 ms       | 1193.018 ms          | 401.261 ms           | Выигрыш в **2.93** раз  |