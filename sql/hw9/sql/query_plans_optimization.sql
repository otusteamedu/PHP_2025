CREATE INDEX IF NOT EXISTS idx_session_start_date ON cinema.session ((start_time::date));
CREATE INDEX IF NOT EXISTS idx_order_created_at ON cinema."order" (created_at);
CREATE INDEX IF NOT EXISTS idx_ticket_order_id ON cinema.ticket (order_id);
CREATE INDEX IF NOT EXISTS idx_ticket_session_id ON cinema.ticket (session_id);
CREATE INDEX IF NOT EXISTS idx_session_movie_id ON cinema.session (movie_id);
CREATE INDEX IF NOT EXISTS idx_session_price_session_id ON cinema.session_price (session_id);
