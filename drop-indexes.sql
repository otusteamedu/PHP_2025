-- Indexes for table orders
DROP INDEX IF EXISTS "idx-orders-paid_at_date";
DROP INDEX IF EXISTS "idx-orders-status";

-- Indexes for table sessions
DROP INDEX IF EXISTS "idx-sessions-movie_id";
DROP INDEX IF EXISTS "idx-sessions-hall_id";
DROP INDEX IF EXISTS "idx-sessions-started_at";
DROP INDEX IF EXISTS "idx-sessions-started_at_date";

-- Indexes for table seat_prices
DROP INDEX IF EXISTS "idx-seat_prices-session_id";
DROP INDEX IF EXISTS "idx-seat_prices-seat_category_id";
DROP INDEX IF EXISTS "idx-seat_prices-price";

-- Indexes for table seats
DROP INDEX IF EXISTS "idx-seats-seat_category_id";
DROP INDEX IF EXISTS "idx-seats-hall_id";
DROP INDEX IF EXISTS "idx-seats-row";
DROP INDEX IF EXISTS "idx-seats-seat_number";

-- Indexes for table tickets
DROP INDEX IF EXISTS "idx-tickets-order_id";
DROP INDEX IF EXISTS "idx-tickets-session_id";
DROP INDEX IF EXISTS "idx-tickets-seat_id";
DROP INDEX IF EXISTS "idx-tickets-price";
