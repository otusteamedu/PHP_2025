
CREATE INDEX idx_session_start_time ON Session(start_time);
CREATE INDEX idx_session_movie_id ON Session(movie_id);

CREATE INDEX idx_ticket_session_id ON Ticket(session_id);
CREATE INDEX idx_ticket_client_id ON Ticket(client_id);
CREATE INDEX idx_ticket_seat_id ON Ticket(seat_id);

CREATE INDEX idx_seats_hall_id ON Seats(hall_id);

CREATE INDEX idx_movie_id ON Movie(movie_id);