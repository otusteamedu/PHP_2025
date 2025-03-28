SELECT
    s.row_number,
    s.seat_number,
    s.type,
    CASE
        WHEN t.id IS NULL THEN 'Свободно'
        ELSE 'Занято'
        END AS status
FROM Seat s
         LEFT JOIN Ticket t ON s.id = t.seat_id AND t.session_id = :session_id
WHERE s.hall_id = (SELECT hall_id FROM Session WHERE id = :session_id)
ORDER BY s.row_number, s.seat_number;