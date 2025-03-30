-- таблица фильмов
CREATE TABLE IF NOT EXISTS public.film
(
    id       UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    title    VARCHAR(255) NOT NULL,
    duration SERIAL,
    price    REAL         NOT NULL
)
;

COMMENT ON TABLE public.film IS 'Фильм, который идет в кинотеатре';

COMMENT ON COLUMN public.film.duration IS 'Продолжительность в секундах';

COMMENT ON COLUMN public.film.price IS 'Базовая цена за просмотр';

ALTER TABLE public.film
    OWNER TO test;

-- таблица рейтинга сеансов
CREATE TABLE IF NOT EXISTS public.session_rating
(
    id     UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    rating REAL             DEFAULT 1 NOT NULL
)
;

COMMENT ON TABLE public.session_rating IS 'Рейтинг сенаса, который будет влиять на стоимость билета';

COMMENT ON COLUMN public.session_rating.rating IS 'Рейтинг сеанса';

ALTER TABLE public.session_rating
    OWNER TO test;

-- таблица залов
CREATE TABLE IF NOT EXISTS public.cinema_room
(
    id     UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    title  VARCHAR(255)               NOT NULL,
    rating REAL             DEFAULT 1 NOT NULL
);

COMMENT ON TABLE public.cinema_room IS 'Кинозал';

COMMENT ON COLUMN public.cinema_room.title IS 'Название';

COMMENT ON COLUMN public.cinema_room.rating IS 'Рейтинг, который влияет на стоимость билета';

ALTER TABLE public.cinema_room
    OWNER TO test;

-- таблица сеансов
CREATE TABLE IF NOT EXISTS public.session
(
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    start_from     TIMESTAMP NOT NULL,
    end_to         TIMESTAMP NOT NULL,
    film_id        UUID      NOT NULL
        CONSTRAINT session_film_id_fk
            REFERENCES public.film,
    rating_id      UUID      NOT NULL
        CONSTRAINT session_session_rating_id_fk
            REFERENCES public.session_rating,
    cinema_room_id UUID      NOT NULL
        CONSTRAINT session_cinema_room_id_fk
            REFERENCES public.cinema_room
);

COMMENT ON TABLE public.session IS 'Сеанс';

COMMENT ON COLUMN public.session.start_from IS 'Дата начала сеанса';

COMMENT ON COLUMN public.session.end_to IS 'Дата завершения сеанса';

ALTER TABLE public.session
    OWNER TO test;

-- таблица мест
CREATE TABLE IF NOT EXISTS public.cinema_room_seat
(
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    row            smallserial,
    place          smallserial,
    cinema_room_id UUID NOT NULL
        CONSTRAINT cinema_room_seat_cinema_room_id_fk
            REFERENCES public.cinema_room,
    rating         REAL             DEFAULT 1,
    CONSTRAINT cinema_room_unique_seat_pk
        unique (row, place, cinema_room_id)
);

COMMENT ON TABLE public.cinema_room_seat IS 'Места кинозала';

COMMENT ON COLUMN public.cinema_room_seat.row IS 'Ряд';

COMMENT ON COLUMN public.cinema_room_seat.place IS 'Место';

COMMENT ON COLUMN public.cinema_room_seat.rating IS 'Рейтинг, который повлияет на стоимость билета';

ALTER TABLE public.cinema_room_seat
    OWNER TO test;

-- таблица билетов
CREATE TABLE IF NOT EXISTS public.ticket
(
    id         UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    price      REAL NOT NULL,
    seat_id    UUID
        CONSTRAINT ticket_cinema_room_seat_id_fk
            REFERENCES public.cinema_room_seat,
    session_id UUID NOT NULL
        CONSTRAINT ticket_session_id_fk
            REFERENCES public.session,
    CONSTRAINT ticket_pk_2
        unique (session_id, seat_id)
);

COMMENT ON TABLE public.ticket IS 'Билет';

COMMENT ON COLUMN public.ticket.price IS 'Цена билета';

COMMENT ON COLUMN public.ticket.seat_id IS 'Идентификатор места, может быть null, когда продаем без места';

ALTER TABLE public.ticket
    OWNER TO test;