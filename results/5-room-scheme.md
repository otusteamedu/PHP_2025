### схема зала на конкретный сеанс

**SQL-запрос:**
```sql
SELECT
    T.row_number, T.seat_number,
    T.price,
    CASE
        WHEN T.order_id>0
            THEN 'Занято'
        ELSE 'Свободно'
        END AS ticket_status
FROM
    tickets T
        JOIN session S ON S.id = T.session_id
        LEFT JOIN orders ORD ON T.order_id = ORD.id

WHERE
    S.id = 6951
ORDER BY
    T.row_number::integer,
    T.seat_number::integer;
```

**Добавлены индексы:**
```sql
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-room_id" ON session (room_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-session_id" ON tickets (session_id);
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id);

-- Для ускорения сортировки
CREATE INDEX IF NOT EXISTS "idx-tickets-row_number" ON tickets (row_number);
CREATE INDEX IF NOT EXISTS "idx-tickets-seat_number" ON tickets (seat_number);
```


|                    | 10k            | 10kk without indexes | 10kk with indexes | Result                 |
|--------------------|:---------------|:---------------------|:------------------|:-----------------------|
| **Cost**           | 198.52..198.53 | 122212.02..122212.02 | 20.98..20.98      | Выигрыш в **5800** раз |
| **Actual time**    | 0.553..0.554   | 172.155..179.099     | 0.057..0.058      |                        |
| **Execution Time** | 0.581 ms       | 179.577 ms           | 0.087 ms          | Выигрыш в **2064** раз |
