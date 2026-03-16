
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
    place smallint not null,
    hall_id smallint not null,
    seat_category cinema.seat_category not null,

    foreign key (hallId) references cinema.hall (id),

-- В одном зале может быть одно место
    unique (hall_id, row, seat)
);

create table cinema.session
(
    id bigserial primary key,
    movie_id bigint not null,
    hall_id smallint not null,
    startTime timestamp not null,
    part_of_day cinema.part_of_day not null,

    foreign key (hall_id) references cinema.hall (id),
    foreign key (movie_id) references cinema.movie (id)
);

create table cinema.orders
(
    id bigserial primary key,
    customer_id bigint not null,
    created_at  timestamp not null default now(),

    foreign key (customer_id) references cinema.customer (id),
);


create table cinema.price
(
    id bigserial primary key,
    movieId bigint not null,
    partOfDay partOfDay not null,
    price money not null,

    foreign key (movieId) references cinema.movie (id)
);

CREATE TABLE cinema.customer_order (
    orderId bigint references cinema.customer,
    customerId bigint references cinema.order,
    PRIMARY KEY (orderId, customerId)
);

create table cinema.orders_place (
    placeId bigint references cinema.place,
    orderId int references cinema.orders,
    primary key (orderId, placeId)
);

CREATE TABLE cinema.place_price (
    priceId bigint references cinema.place,
    placeId int references cinema.price,
    PRIMARY KEY (priceId, placeId)
);