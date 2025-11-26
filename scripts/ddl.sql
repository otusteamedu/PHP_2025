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

create unique index seances_id_hall_id_uindex
    on public.seances (id, hall_id);

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

create unique index places_hall_id_number_uindex
    on public.places (hall_id, number);

create unique index places_id_hall_id_uindex
    on public.places (id, hall_id);

create table public.tickets
(
    id        bigserial
        primary key,
    price     integer not null,
    place_id  bigint  not null,
    seance_id bigint  not null,
    hall_id   bigint  not null
        references public.halls,
    unique (place_id, seance_id),
    foreign key (place_id, hall_id) references public.places (id, hall_id),
    foreign key (seance_id, hall_id) references public.seances (id, hall_id)
);
