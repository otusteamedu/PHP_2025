-- Выборка самого прибыльного фильма
SELECT
    M.title AS movie_title,
    SUM(T.price) AS total_revenue
FROM
    movies M
JOIN
    sessions S ON M.id = S.movie_id
JOIN
    tickets T ON S.id = T.session_id
JOIN
    order_ticket OT ON T.id = OT.ticket_id
JOIN
    orders O ON OT.order_id = O.id
WHERE
    O.status = 'paid'
GROUP BY
    M.id, M.title
ORDER BY
    total_revenue DESC
LIMIT 1;