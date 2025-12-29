-- drop table cinema.customer, cinema.cinema, cinema.movie, cinema.place, cinema.hall, cinema.session;
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

create table cinema.place
(
    id serial primary key,
    row smallint not null,
    place smallint not null
);

create table cinema.hall
(
    id smallserial primary key,
    cinemaId smallint not null,
    number smallint not null,

    foreign key (cinemaId) references cinema.cinema (id)
);

create table cinema.session
(
    id bigserial primary key,
    movieId bigint not null,
    hallId smallint not null,
    startTime timestamp not null,

    foreign key (hallId) references cinema.hall (id),
    foreign key (movieId) references cinema.movie (id)
);