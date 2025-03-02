create table if not exists public.film
(
    id
    varchar
(
    36
) not null
    constraint film_pk
    primary key,
    title varchar
(
    255
) not null,
    duration serial,
    price real not null
    );

comment
on table public.film is 'Фильм, который идет в кинотеатре';

comment
on column public.film.duration is 'Продолжительность в секундах';

comment
on column public.film.price is 'Базовая цена за просмотр';

alter table public.film
    owner to test;

create table if not exists public.session_rating
(
    id
    varchar
(
    36
) not null
    constraint session_rating_pk
    primary key,
    rating real default 1 not null
    );

comment
on table public.session_rating is 'Рейтинг сенаса, который будет влиять на стоимость билета';

comment
on column public.session_rating.rating is 'Рейтинг сеанса';

alter table public.session_rating
    owner to test;

create table if not exists public.cinema_room
(
    id
    varchar
(
    36
) not null
    constraint cinema_room_pk
    primary key,
    title varchar
(
    255
) not null,
    rating real default 1 not null
    );

comment
on table public.cinema_room is 'Кинозал';

comment
on column public.cinema_room.title is 'Название';

comment
on column public.cinema_room.rating is 'Рейтинг, который влияет на стоимость билета';

alter table public.cinema_room
    owner to test;

create table if not exists public.session
(
    id
    varchar
(
    36
) not null
    constraint session_pk
    primary key,
    start_from timestamp not null,
    end_to timestamp not null,
    film_id varchar
(
    36
) not null
    constraint session_film_id_fk
    references public.film,
    rating_id varchar
(
    36
) not null
    constraint session_session_rating_id_fk
    references public.session_rating,
    cinema_room_id varchar
(
    36
) not null
    constraint session_cinema_room_id_fk
    references public.cinema_room
    );

comment
on table public.session is 'Сеанс';

comment
on column public.session.start_from is 'Дата начала сеанса';

comment
on column public.session.end_to is 'Дата завершения сеанса';

alter table public.session
    owner to test;

create table if not exists public.cinema_room_seat
(
    id
    varchar
(
    36
) not null
    constraint cinema_room_seat_pk
    primary key,
    row smallserial,
    place smallserial,
    cinema_room_id varchar
(
    36
) not null
    constraint cinema_room_seat_cinema_room_id_fk
    references public.cinema_room,
    rating real default 1,
    constraint cinema_room_unique_seat_pk
    unique
(
    row,
    place,
    cinema_room_id
)
    );

comment
on table public.cinema_room_seat is 'Места кинозала';

comment
on column public.cinema_room_seat.row is 'Ряд';

comment
on column public.cinema_room_seat.place is 'Место';

comment
on column public.cinema_room_seat.rating is 'Рейтинг, который повлияет на стоимость билета';

alter table public.cinema_room_seat
    owner to test;

create table if not exists public.ticket
(
    id
    varchar
(
    36
) not null
    constraint ticket_pk
    primary key,
    price real not null,
    seat_id varchar
(
    36
)
    constraint ticket_cinema_room_seat_id_fk
    references public.cinema_room_seat,
    session_id varchar
(
    36
) not null
    constraint ticket_session_id_fk
    references public.session,
    constraint ticket_pk_2
    unique
(
    session_id,
    seat_id
)
    );

comment
on table public.ticket is 'Билет';

comment
on column public.ticket.price is 'Цена билета';

comment
on column public.ticket.seat_id is 'Идентификатор места, может быть null, когда продаем без места';

alter table public.ticket
    owner to test;

