create table public.halls
(
    id    bigserial
        constraint halls_pk
            primary key,
    title varchar not null
);

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
    id       bigserial
        constraint tickets_pk
            primary key,
    price    integer not null,
    price_id bigint  not null
        constraint tickets_prices_id_fk
            references public.prices
);

create index tickets_price_id_index
    on public.tickets (price_id);

create table public.places
(
    id      bigserial
        constraint places_pk
            primary key,
    number  varchar not null,
    hall_id bigint  not null
        constraint places_halls_id_fk
            references public.halls
);

create index places_hall_id_index
    on public.places (hall_id);

create table public.prices
(
    id        bigint  not null
        constraint prices_pk
            primary key,
    value     integer not null,
    place_id  bigint  not null
        constraint prices_places_id_fk
            references public.places,
    seance_id bigint  not null
        constraint prices_seances_id_fk
            references public.seances
);

create index prices_place_id_index
    on public.prices (place_id);

create unique index prices_place_id_seance_id_uindex
    on public.prices (place_id, seance_id);

create index prices_seance_id_index
    on public.prices (seance_id);
