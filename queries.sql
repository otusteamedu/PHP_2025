EXPLAIN ANALYZE
SELECT DISTINCT m.title
FROM Movie m
JOIN Session s ON m.movie_id = s.movie_id
WHERE s.start_time::date = CURRENT_DATE;

EXPLAIN ANALYZE
SELECT COUNT(*) AS total_sold_tickets
FROM Ticket t
JOIN Session s ON t.session_id = s.session_id
WHERE s.start_time >= NOW() - INTERVAL '7 days';

EXPLAIN ANALYZE
SELECT m.title, s.start_time, s.end_time
FROM Movie m
JOIN Session s ON m.movie_id = s.movie_id
WHERE s.start_time::date = CURRENT_DATE
ORDER BY s.start_time;

EXPLAIN ANALYZE
SELECT m.title, SUM(t.price) AS total_revenue
FROM Movie m
JOIN Session s ON m.movie_id = s.movie_id
JOIN Ticket t ON s.session_id = t.session_id
WHERE s.start_time >= NOW() - INTERVAL '7 days'
GROUP BY m.title
ORDER BY total_revenue DESC
LIMIT 3;

EXPLAIN ANALYZE
SELECT s.row_number, s.seat_number, 
       CASE 
           WHEN t.ticket_id IS NULL THEN 'Свободно' 
           ELSE 'Занято' 
       END AS seat_status
FROM Seats s
LEFT JOIN Ticket t ON s.seat_id = t.seat_id
WHERE s.hall_id = (SELECT hall_id FROM Session WHERE session_id = 147)
ORDER BY s.row_number, s.seat_number;

EXPLAIN ANALYZE
SELECT MIN(t.price) AS min_price, MAX(t.price) AS max_price
FROM Ticket t
WHERE t.session_id = 150;

