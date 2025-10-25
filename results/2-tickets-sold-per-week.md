### Подсчёт проданных билетов за неделю

**SQL-запрос:**
```sql
EXPLAIN ANALYZE
SELECT
    COUNT(t.id) as tickets_num
FROM
    tickets t
        JOIN orders o ON o.id = t.order_id
WHERE
    o.status = 'paid'
    AND DATE(o.paid_at) >= CURRENT_DATE - INTERVAL '1 week' AND DATE(o.paid_at) < CURRENT_DATE;
```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по статусу заказа
CREATE INDEX IF NOT EXISTS "idx-orders-status" ON orders (status);

-- Для ускорения фильтрации по дате оплаты заказа
CREATE INDEX IF NOT EXISTS "idx-orders-paid_at_date" ON orders (DATE(paid_at));

-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id);   
```


|                    | 10k            | 10kk without indexes | 10kk with indexes  | Result                  |
|--------------------|:---------------|:---------------------|:-------------------|:------------------------|
| **Cost**           | 564.52..564.53 | 158468.31..158468.32 | 70919.93..70919.94 | Выигрыш в **2.23** раз  |
| **Actual time**    | 2.577..2.578   | 860.492..865.988     | 360.845..370.062   |                         |
| **Execution Time** | 2.597 ms       | 866.023 ms           | 370.162 ms         | Выигрыш в **2.34** раза |