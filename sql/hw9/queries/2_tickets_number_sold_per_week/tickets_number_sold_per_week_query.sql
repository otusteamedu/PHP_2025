-- 2. Подсчёт проданных билетов за неделю

EXPLAIN ANALYZE
SELECT
    count(*) AS sold_tickets_number
FROM
    cinema.ticket t
JOIN
    cinema."order" o
    ON t.order_id = o.id
WHERE
    o.created_at BETWEEN
        current_date - 7
        AND current_date - 1;
