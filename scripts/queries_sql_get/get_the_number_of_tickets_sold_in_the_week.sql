SELECT COUNT(*) AS tickets_sold
FROM Ticket t
         JOIN Session s ON t.session_id = s.id
WHERE s.start_time BETWEEN NOW() - INTERVAL '7 days' AND NOW();