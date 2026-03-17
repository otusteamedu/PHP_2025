
drop schema if exists cinema cascade;
create schema cinema;

create type cinema.seat_category as enum
    (
    'в центре',
    'с краю',
    'в центре, далеко',
    'с краю далеко'
);

create type cinema.part_of_day as enum
    (
    'утро',
    'день',
    'вечер',
    'ночь'
);

create table cinema.customer
(
    id bigserial primary key,
    name character varying(50),
    email character varying(50) not null,
    phone character varying(20) not null
);

create table cinema.cinema
(
    id smallserial primary key,
    city character varying(50) not null,
    address text not null
);

create table cinema.movie
(
    id bigserial primary key,
    name character varying(100) not null
);

create table cinema.hall
(
    id smallserial primary key,
    cinema_id smallint not null,
    number smallint not null,

    foreign key (cinema_id) references cinema.cinema (id)
);


create table cinema.place
(
    id serial primary key,
    row smallint not null,
    seat smallint not null,
    hall_id smallint not null,
    seat_category cinema.seat_category not null,

    foreign key (hall_id) references cinema.hall (id),

-- В одном зале может быть одно место
    unique (hall_id, row, seat)
);

create table cinema.session
(
    id bigserial primary key,
    movie_id bigint not null,
    hall_id smallint not null,
    start_time timestamp not null,
    part_of_day cinema.part_of_day not null,

    foreign key (hall_id) references cinema.hall (id),
    foreign key (movie_id) references cinema.movie (id)
);

create table cinema.orders
(
    id bigserial primary key,
    customer_id bigint not null,
    created_at  timestamp not null default now(),

    foreign key (customer_id) references cinema.customer (id)
);


create table cinema.price
(
    id bigserial primary key,
    session_id bigint not null,
    seat_category cinema.seat_category not null,
    price money not null,

    foreign key (session_id) references cinema.session (id),

    -- для каждой категории может быть только одна цена
    unique (session_id, seat_category)
);

create table cinema.ticket
(
    id bigserial primary key,
    session_id bigint not null,
    hall_id smallint not null,
    order_id bigint not null,
    place_id bigint not null,
    price money not null,

    foreign key (session_id) references cinema.session(id),
    foreign key (hall_id) references cinema.hall(id),
    foreign key (order_id) references cinema.orders(id),
    foreign key (place_id) references cinema.place(id)
);

-- Не позволит покупать один и тот же билет в один сеанс
create unique index unique_ticket
on cinema.ticket(session_id, place_id);