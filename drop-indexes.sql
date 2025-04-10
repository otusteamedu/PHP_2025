DROP INDEX IF EXISTS "idx-orders-created_at";
DROP INDEX IF EXISTS "idx-orders-status";

DROP INDEX IF EXISTS "idx-sessions-film_id";
DROP INDEX IF EXISTS "idx-sessions-room_id";
DROP INDEX IF EXISTS "idx-sessions-start_time";

DROP INDEX IF EXISTS "idx-tickets-order_id";
DROP INDEX IF EXISTS "idx-tickets-session_id";
DROP INDEX IF EXISTS "idx-tickets-row_number";
DROP INDEX IF EXISTS "idx-tickets-seat_number";
DROP INDEX IF EXISTS "idx-tickets-price";