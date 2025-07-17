create table public.movies
(
    movie_id         serial
        primary key,
    title            varchar(100) not null,
    description      text,
    duration_minutes integer      not null,
    rating           varchar(10),
    genre            varchar(50),
    release_date     date,
    created_at       timestamp default CURRENT_TIMESTAMP
);

alter table public.movies
    owner to otus_cinema;

create table public.halls
(
    hall_id    serial
        primary key,
    name       varchar(50) not null,
    hall_type  varchar(20) not null
        constraint halls_hall_type_check
            check ((hall_type)::text = ANY
                   ((ARRAY ['2D'::character varying, '3D'::character varying, 'IMAX'::character varying, 'VIP'::character varying])::text[])),
    capacity   integer,
    created_at timestamp default CURRENT_TIMESTAMP
);

alter table public.halls
    owner to otus_cinema;

create table public.hall_layouts
(
    layout_id     serial
        primary key,
    hall_id       integer
        references public.halls,
    rows_count    integer not null,
    seats_per_row integer not null,
    layout_json   jsonb   not null,
    created_at    timestamp default CURRENT_TIMESTAMP
);

alter table public.hall_layouts
    owner to otus_cinema;

create table public.seats
(
    seat_id     serial
        primary key,
    hall_id     integer
        references public.halls,
    row_letter  char        not null,
    seat_number integer     not null,
    seat_type   varchar(20) not null
        constraint seats_seat_type_check
            check ((seat_type)::text = ANY
                   ((ARRAY ['standard'::character varying, 'vip'::character varying, 'sofa'::character varying, 'disabled'::character varying])::text[])),
    zone        varchar(20),
    created_at  timestamp default CURRENT_TIMESTAMP,
    unique (hall_id, row_letter, seat_number)
);

alter table public.seats
    owner to otus_cinema;

create index idx_seats_hall
    on public.seats (hall_id);

create table public.screenings
(
    screening_id serial
        primary key,
    movie_id     integer
        references public.movies,
    hall_id      integer
        references public.halls,
    start_time   timestamp      not null,
    end_time     timestamp      not null,
    format       varchar(20)    not null
        constraint screenings_format_check
            check ((format)::text = ANY
                   (ARRAY [('2D'::character varying)::text, ('3D'::character varying)::text, ('IMAX'::character varying)::text, ('4DX'::character varying)::text, ('VIP'::character varying)::text])),
    base_price   numeric(10, 2) not null,
    created_at   timestamp default CURRENT_TIMESTAMP
);

alter table public.screenings
    owner to otus_cinema;

create index idx_screenings_movie
    on public.screenings (movie_id);

create index idx_screenings_hall
    on public.screenings (hall_id);

create table public.prices
(
    price_id     serial
        primary key,
    screening_id integer
        references public.screenings,
    seat_type    varchar(20)    not null,
    price        numeric(10, 2) not null,
    created_at   timestamp default CURRENT_TIMESTAMP,
    unique (screening_id, seat_type)
);

alter table public.prices
    owner to otus_cinema;

create index idx_prices_screening
    on public.prices (screening_id);

create table public.customers
(
    customer_id       serial
        primary key,
    first_name        varchar(50) not null,
    last_name         varchar(50) not null,
    email             varchar(100)
        unique,
    phone             varchar(20),
    registration_date timestamp default CURRENT_TIMESTAMP
);

alter table public.customers
    owner to otus_cinema;

create table public.tickets
(
    ticket_id     serial
        primary key,
    screening_id  integer
        references public.screenings,
    seat_id       integer
        references public.seats,
    customer_id   integer
        references public.customers,
    price         numeric(10, 2) not null,
    purchase_time timestamp default CURRENT_TIMESTAMP,
    status        varchar(20)    not null
        constraint tickets_status_check
            check ((status)::text = ANY
                   ((ARRAY ['active'::character varying, 'cancelled'::character varying, 'used'::character varying])::text[])),
    unique (screening_id, seat_id)
);

alter table public.tickets
    owner to otus_cinema;

create index idx_tickets_screening
    on public.tickets (screening_id);

create index idx_tickets_customer
    on public.tickets (customer_id);

create table public.staff
(
    staff_id   serial
        primary key,
    first_name varchar(50) not null,
    last_name  varchar(50) not null,
    position   varchar(50) not null,
    email      varchar(100)
        unique,
    phone      varchar(20),
    hire_date  date        not null
);

alter table public.staff
    owner to otus_cinema;