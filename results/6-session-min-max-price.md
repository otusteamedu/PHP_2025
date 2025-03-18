### Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс

**SQL-запрос:**
```sql
SELECT
    MIN(price) as min_price,
    MAX(price) as max_price
FROM
    seat_prices
WHERE
    session_id = 70;
```

**Добавлены индексы:**
```sql
-- Для ускорения фильтрации по сеансу
CREATE INDEX IF NOT EXISTS "idx-seat_prices-session_id" ON seat_prices (session_id);

-- Для ускорения агрегации по стоимости
CREATE INDEX IF NOT EXISTS "idx-seat_prices-price" ON seat_prices (price);
```


|                    | 10k            | 10kk without indexes | 10kk with indexes | Result                 |
|--------------------|:---------------|:---------------------|:------------------|:-----------------------|
| **Cost**           | 189.01..189.02 | 58889.88..58889.89   | 12.48..12.49      | Выигрыш в **4711** раз |
| **Actual time**    | 1.492..1.493   | 273.753..280.971     | 0.100..0.100      |                        |
| **Execution Time** | 1.511 ms       | 280.994 ms           | 0.126 ms          | Выигрыш в **2230** раз |