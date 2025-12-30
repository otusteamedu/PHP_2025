-- drop table cinema.customer, cinema.cinema, cinema.movie, cinema.place, cinema.hall, cinema.session, cinema.order, cinema.price;
-- drop table cinema.customer_order, cinema.order_place, cinema.place_price;
create schema cinema;

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
    cinemaId smallint not null,
    number smallint not null,

    foreign key (cinemaId) references cinema.cinema (id)
);

CREATE TYPE placeCategory AS ENUM ('В центре', 'С краю', 'В центре, далеко', 'С краю далеко');

create table cinema.place
(
    id serial primary key,
    row smallint not null,
    place smallint not null,
    hallId smallint not null,
    orderId bigint not null,
    placeId int not null,
    category placeCategory not null,

    foreign key (hallId) references cinema.hall (id),
    foreign key (placeId) references cinema.place (id),
    foreign key (orderId) references cinema.order (id)
);

create table cinema.session
(
    id bigserial primary key,
    movieId bigint not null,
    hallId smallint not null,
    orderId bigint not null,
    startTime timestamp not null,

    foreign key (hallId) references cinema.hall (id),
    foreign key (orderId) references cinema.order (id),
    foreign key (movieId) references cinema.movie (id)
);

create table cinema.order
(
    id bigserial primary key,
    customerId bigint not null,
    sessionId bigint not null,
    placeId int not null,
    ticketPrice money not null,
    time timestamp not null,

    foreign key (customerId) references cinema.customer (id),
    foreign key (sessionId) references cinema.session (id),
    foreign key (placeId) references cinema.place (id)
);

CREATE TYPE partOfDay AS ENUM ('Утро', 'День', 'Вечер', 'Ночь');

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

CREATE TABLE cinema.order_place (
    orderId bigint references cinema.place,
    placeId int references cinema.order,
    PRIMARY KEY (orderId, placeId)
);

CREATE TABLE cinema.place_price (
    priceId bigint references cinema.place,
    placeId int references cinema.price,
    PRIMARY KEY (priceId, placeId)
);