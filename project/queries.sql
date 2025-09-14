SELECT DISTINCT m.title
FROM Movies m
         JOIN Sessions s ON m.movie_id = s.movie_id
WHERE DATE(s.start_time) = CURRENT_DATE;


SELECT COUNT(*)
FROM Tickets
WHERE purchase_time >= CURRENT_DATE - INTERVAL '7 days';


SELECT m.title, s.start_time
FROM Movies m
         JOIN Sessions s ON m.movie_id = s.movie_id
WHERE DATE(s.start_time) = CURRENT_DATE
ORDER BY s.start_time;


SELECT m.title, SUM(t.price) AS revenue
FROM Tickets t
         JOIN Sessions s ON t.session_id = s.session_id
         JOIN Movies m ON s.movie_id = m.movie_id
WHERE t.purchase_time >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY m.title
ORDER BY revenue DESC
    LIMIT 3;


SELECT se.row_number, se.seat_number, se.seat_type,
       CASE WHEN t.ticket_id IS NULL THEN 'free' ELSE 'occupied' END AS status
FROM Sessions s
         JOIN Seats se ON s.hall_id = se.hall_id
         LEFT JOIN Tickets t ON t.session_id = s.session_id AND t.seat_id = se.seat_id
WHERE s.session_id = 101362
ORDER BY se.row_number, se.seat_number;


SELECT MIN(price), MAX(price)
FROM TicketPrices
WHERE session_id = 101362;