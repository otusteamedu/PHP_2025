SELECT
    MIN(price) AS min_price,
    MAX(price) AS max_price
FROM Ticket
WHERE session_id = :session_id;