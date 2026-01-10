-- таблица сеансов
-- Для ускорения фильтрации по дате начала и конца сеанса
CREATE INDEX IF NOT EXISTS "idx_session_start_from_date" ON session USING BRIN (DATE(start_from));
CREATE INDEX IF NOT EXISTS "idx_session_start_from_date" ON session USING BRIN (DATE(end_to));
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx_session_film_id" ON session (film_id);
CREATE INDEX IF NOT EXISTS "idx_session_rating_id" ON session (rating_id);
CREATE INDEX IF NOT EXISTS "idx_session_cinema_room_id" ON session (cinema_room_id);

-- таблица билетов
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx_ticket_session_id" ON ticket (session_id);
CREATE INDEX IF NOT EXISTS "idx_ticket_seat_id" ON ticket (seat_id);
-- Для подсчета стоимости билетов
CREATE INDEX IF NOT EXISTS "idx_ticket_price" ON ticket (price);

-- таблица мест
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx_cinema_room_seat_cinema_room_id" ON cinema_room_seat (cinema_room_id);

