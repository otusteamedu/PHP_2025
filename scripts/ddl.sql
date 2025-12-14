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

create table public.movie_entity_attribute_types
(
    id   bigserial
        constraint movie_entity_attribute_types_pk
            primary key,
    type varchar not null
        constraint movie_entity_attribute_types_type_check
            check (
                (type)::text = ANY
                ((ARRAY [
                    'string'::character varying,
                    'text'::character varying,
                    'integer'::character varying,
                    'double'::character varying,
                    'boolean'::character varying,
                    'date'::character varying,
                    'time'::character varying,
                    'datetime'::character varying
                    ])::text[])
                )
);

create unique index movie_entity_attribute_types_type_uindex
    on public.movie_entity_attribute_types (type);

create table public.movie_entity_attributes
(
    id                             bigserial
        constraint movie_entity_attributes_pk
            primary key,
    movie_entity_attribute_type_id bigint   not null
        constraint movie_entity_attributes_movie_entity_attribute_types_id_fk
            references public.movie_entity_attribute_types
            on update restrict on delete cascade,
    attribute                      varchar  not null,
    mode                           smallint not null
);

create index movie_entity_attributes_movie_entity_attribute_type_id_index
    on public.movie_entity_attributes (movie_entity_attribute_type_id);

create index movie_entity_attributes_mode_index
    on public.movie_entity_attributes (mode);

create table public.movie_entity_values
(
    id                        bigserial
        constraint movie_entity_values_pk
            primary key,
    movie_entity_attribute_id bigint not null
        constraint movie_entity_values_movie_entity_attributes_id_fk
            references public.movie_entity_attributes
            on update restrict on delete cascade,
    movie_id                  bigint not null
        constraint movie_entity_values_movies_id_fk
            references public.movies
            on update restrict on delete cascade,
    value_string              varchar,
    value_text                text,
    value_integer             bigint,
    value_double              double precision,
    value_boolean             boolean,
    value_date                date,
    value_time                time,
    value_datetime            timestamp,
    constraint movie_entity_values_exactly_one_value_check
        check ((((((((((value_string IS NOT NULL))::integer + ((value_text IS NOT NULL))::integer) +
                     ((value_integer IS NOT NULL))::integer) + ((value_double IS NOT NULL))::integer) +
                   ((value_boolean IS NOT NULL))::integer) + ((value_date IS NOT NULL))::integer) +
                 ((value_time IS NOT NULL))::integer) + ((value_datetime IS NOT NULL))::integer) = 1)
);

create index movie_entity_values_movie_entity_attribute_id_index
    on public.movie_entity_values (movie_entity_attribute_id);

create index movie_entity_values_movie_id_index
    on public.movie_entity_values (movie_id);

create unique index movie_entity_values_movie_entity_attribute_id_movie_id_uindex
    on public.movie_entity_values (movie_entity_attribute_id, movie_id);

create view public.movie_attributes(title, type, attribute, mode, value) as
SELECT m.title,
       meat.type,
       mea.attribute,
       mea.mode,
       COALESCE(
           mev.value_string,
           mev.value_text::character varying,
           mev.value_integer::text::character varying,
           mev.value_double::text::character varying,
           mev.value_boolean::text::character varying,
           mev.value_date::text::character varying,
           mev.value_time::text::character varying,
           mev.value_datetime::text::character varying
       ) AS value
FROM movies m
         JOIN movie_entity_values mev ON m.id = mev.movie_id
         JOIN movie_entity_attributes mea ON mea.id = mev.movie_entity_attribute_id
         JOIN movie_entity_attribute_types meat ON meat.id = mea.movie_entity_attribute_type_id
ORDER BY m.id, mea.id;

create view public.movie_attributes_public(title, type, attribute, value) as
SELECT title,
       type,
       attribute,
       value
FROM movie_attributes
WHERE (mode::integer & 1) <> 0;

create view public.movie_attributes_service(title, type, attribute, value) as
SELECT title,
       type,
       attribute,
       value
FROM movie_attributes
WHERE (mode::integer & 2) <> 0;

create view public.movie_attributes_marketing(title, type, attribute, value) as
SELECT title,
       type,
       attribute,
       value
FROM movie_attributes
WHERE (mode::integer & 4) <> 0;
