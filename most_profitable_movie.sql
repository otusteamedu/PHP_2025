-- Самый прибыльный фильм (по сумме выручки с учётом возвратов)
-- Логика: суммируем net_amount для билетов, где
-- net_amount = CASE
--   WHEN status = 'sold' THEN price_amount
--   WHEN status = 'refunded' THEN GREATEST(price_amount - COALESCE(refund_amount, 0), 0)
--   ELSE 0
-- END
-- Игнорируем 'reserved' и 'cancelled' для выручки.

WITH ticket_cashflow AS (
    SELECT
        t.id,
        s.movie_id,
        CASE
            WHEN t.status = 'sold' THEN t.price_amount
            WHEN t.status = 'refunded' THEN GREATEST(t.price_amount - COALESCE(t.refund_amount, 0), 0)
            ELSE 0
        END AS net_amount
    FROM ticket t
    JOIN showtime s ON s.id = t.showtime_id
)
SELECT m.id AS movie_id, m.title, COALESCE(SUM(tc.net_amount), 0) AS revenue
FROM movie m
JOIN showtime s ON s.movie_id = m.id
LEFT JOIN ticket_cashflow tc ON tc.movie_id = m.id
GROUP BY m.id, m.title
ORDER BY revenue DESC, m.title ASC
LIMIT 1;
