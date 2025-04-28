CREATE INDEX idx_tickets_session_id ON public.tickets(session_id);

CREATE INDEX idx_sessions_session_start ON public.sessions(session_start);

CREATE INDEX idx_sessions_id ON public.sessions(id);

CREATE INDEX idx_sessions_film_id ON public.sessions(film_id);

CREATE INDEX idx_seats_id ON public.seats(id);

CREATE INDEX idx_seats_hall_id ON public.seats(hall_id);

CREATE INDEX idx_tickets_seat_session ON public.tickets(seat_id, session_id);

CREATE INDEX idx_seats_row_number ON public.seats(seat_row, seat_number);