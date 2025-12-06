create table public.halls
(
    id    bigserial
        constraint halls_pk
            primary key,
    title varchar not null
);

create table public.hall_seats
(
    id        bigserial
        constraint hall_seats_pk
            primary key,
    hall_id   bigint  not null
        constraint hall_seats_halls_id_fk
            references public.halls,
    number    varchar,
    row       integer not null,
    col       integer not null,
    seat_type varchar not null
);

create index hall_seats_hall_id_index
    on public.hall_seats (hall_id);

create unique index hall_seats_hall_id_row_col_uindex
    on public.hall_seats (hall_id, row, col);

create unique index hall_seats_hall_id_number_uindex
    on public.hall_seats (hall_id, number);

create table public.movies
(
    id    bigserial
        constraint movies_pk
            primary key,
    title varchar not null
);

create table public.seances
(
    id                    bigserial
        constraint seances_pk
            primary key,
    begin_at              timestamp not null,
    end_at                timestamp not null,
    hall_id               bigint    not null
        constraint seances_halls_id_fk
            references public.halls,
    movie_id              bigint    not null
        constraint seances_movies_id_fk
            references public.movies,
    begin_at_range_end_at tsrange generated always as (tsrange(begin_at, end_at)) stored,
    price                 integer   not null,
    constraint seances_no_overlap
        exclude using gist (hall_id with =, begin_at_range_end_at with &&),
    constraint seances_end_at_gt_begin_at
        check (end_at > begin_at)
);

create index seances_hall_id_index
    on public.seances (hall_id);

create index seances_movie_id_index
    on public.seances (movie_id);

create table public.tickets
(
    id           bigserial
        constraint tickets_pk
            primary key,
    price        integer not null,
    seance_id    bigint  not null
        constraint tickets_seances_id_fk
            references public.seances,
    hall_seat_id bigint  not null
        constraint tickets_hall_seats_id_fk
            references public.hall_seats
);

create index tickets_hall_seat_id_index
    on public.tickets (hall_seat_id);

create unique index tickets_seance_id_hall_seat_id_uindex
    on public.tickets (seance_id, hall_seat_id);

create index tickets_seance_id_index
    on public.tickets (seance_id);
