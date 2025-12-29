-- drop table cinema.customer, cinema.cinema, cinema.movie;
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