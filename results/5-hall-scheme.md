### Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

**SQL-запрос:**
```sql
SELECT
    s.id, s.row, s.seat_number,
    sp.price,
    CASE
        WHEN o.status = 'paid'
            THEN 'Занято'
        ELSE 'Свободно'
        END AS seat_status
FROM
    seats s
        JOIN halls h ON h.id = s.hall_id
        JOIN sessions s2 on s2.hall_id = h.id
        JOIN seat_prices sp ON sp.seat_category_id = s.seat_category_id and sp.session_id = s2.id
        LEFT JOIN tickets t ON t.session_id = s2.id AND t.seat_id = s.id
        LEFT JOIN orders o ON o.id = t.order_id
WHERE
    s2.id = 70
ORDER BY
    s.row,
    s.seat_number;
```

**Добавлены индексы:**
```sql
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-hall_id" ON sessions (hall_id);
CREATE INDEX IF NOT EXISTS "idx-seats-hall_id" ON seats (hall_id);
CREATE INDEX IF NOT EXISTS "idx-seats-seat_category_id" ON seats (seat_category_id);
CREATE INDEX IF NOT EXISTS "idx-seat_prices-session_id" ON seat_prices (session_id);
CREATE INDEX IF NOT EXISTS "idx-seat_prices-seat_category_id" ON seat_prices (seat_category_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-session_id" ON tickets (session_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-seat_id" ON tickets (seat_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id);

-- Для ускорения сортировки
CREATE INDEX IF NOT EXISTS "idx-seats-row" ON seats (row);
CREATE INDEX IF NOT EXISTS "idx-seats-seat_number" ON seats (seat_number); 
```


|                    | 10k            | 10kk without indexes | 10kk with indexes | Result                 |
|--------------------|:---------------|:---------------------|:------------------|:-----------------------|
| **Cost**           | 606.53..606.71 | 174969.82..175006.45 | 108.52..109.46    | Выигрыш в **1605** раз |
| **Actual time**    | 2.897..2.901   | 880.260..886.624     | 0.718..0.733      |                        |
| **Execution Time** | 3.008 ms       | 886.754 ms           | 0.829 ms          | Выигрыш в **1070** раз |