-- таблица сеансов
-- Для ускорения фильтрации по дате начала сеанса
DROP INDEX IF EXISTS "idx_session_start_from";
DROP INDEX IF EXISTS "idx_session_start_from_date";

-- Для ускорения JOIN
DROP INDEX IF EXISTS "idx_session_film_id";

-- таблица билетов
-- Для ускорения JOIN
DROP INDEX IF EXISTS "idx_ticket_session_id";
-- Для подсчета стоимости билетов
DROP INDEX IF EXISTS "idx_ticket_price";

-- таблица мест
DROP INDEX IF EXISTS "idx_cinema_room_seat_cinema_room_id";


-- таблица сеансов
-- Для ускорения фильтрации по дате начала сеанса
DROP INDEX IF EXISTS "idx_session_start_from_date";
DROP INDEX IF EXISTS "idx_session_start_from_date";
-- Для ускорения JOIN
DROP INDEX IF EXISTS "idx_session_film_id";
DROP INDEX IF EXISTS "idx_session_rating_id";
DROP INDEX IF EXISTS "idx_session_cinema_room_id";

-- таблица билетов
-- Для ускорения JOIN
DROP INDEX IF EXISTS "idx_ticket_session_id";

-- Для подсчета стоимости билетов
DROP INDEX IF EXISTS "idx_ticket_price";
DROP INDEX IF EXISTS "idx_ticket_session_id";
DROP INDEX IF EXISTS "idx_ticket_seat_id";

-- таблица мест
-- Для ускорения JOIN
DROP INDEX IF EXISTS "idx_cinema_room_seat_cinema_room_id";
