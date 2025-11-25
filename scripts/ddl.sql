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
    id       bigserial
        constraint seances_pk
            primary key,
    begin_at timestamp not null,
    end_at   timestamp not null,
    hall_id  bigint    not null
        constraint seances_halls_id_fk
            references public.halls,
    movie_id bigint    not null
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
    id        bigserial
        constraint tickets_pk
            primary key,
    seance_id bigint  not null
        constraint tickets_seances_id_fk
            references public.seances,
    price     integer not null
);

create index tickets_seance_id_index
    on public.tickets (seance_id);
