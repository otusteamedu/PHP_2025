-- drop database otus_cinema_db;
create database if not exists otus_cinema_db;

create table if not exists otus_cinema_db.cinema (
    id bigint unsigned not null primary key,
    name varchar(255) not null,
    info text not null,
    isActive bool not null,
    createdAt datetime not null,
    updatedAt datetime not null
    );

create table if not exists otus_cinema_db.hall (
    id bigint unsigned not null primary key,
    name varchar(255) not null,
    cinemaId bigint unsigned not null,
    createdAt datetime not null,
    updatedAt datetime not null,
    foreign key (cinemaId) references cinema(id)
    );

create table if not exists otus_cinema_db.movie (
    id bigint unsigned not null primary key,
    name varchar(255) not null,
    duration bigint unsigned not null,
    age int not null,
    createdAt datetime not null,
    updatedAt datetime not null
    );

-- defaultPrice is a * 100 value, f.e. 104050 = 1040.50
create table if not exists otus_cinema_db.session (
    id bigint unsigned not null primary key,
    hallId bigint unsigned not null,
    movieId bigint unsigned not null,
    defaultPrice bigint unsigned not null,
    startsAt datetime not null,
    createdAt datetime not null,
    updatedAt datetime not null,
    foreign key (hallId) references hall(id),
    foreign key (movieId) references movie(id)
    );

-- priceModifier is a percent value, f.e. 120 = 120% = x1.2
create table if not exists otus_cinema_db.seatType (
    id bigint unsigned not null primary key,
    name varchar(255) not null,
    priceModifier int not null,
    createdAt datetime not null,
    updatedAt datetime not null
    );

create table if not exists otus_cinema_db.seat (
    id bigint unsigned not null primary key,
    seatTypeId bigint unsigned not null,
    hallId bigint unsigned not null,
    coordinates json not null,
    number varchar(20) not null,
    createdAt datetime not null,
    updatedAt datetime not null,
    foreign key (seatTypeId) references seatType(id),
    foreign key (hallId) references hall(id)
    );

create table if not exists otus_cinema_db.customer (
    id bigint unsigned not null primary key,
    email varchar(255) not null,
    birthDate date default null,
    name varchar(255) not null,
    password varchar(255) not null,
    createdAt datetime not null,
    updatedAt datetime not null
    );

create table if not exists otus_cinema_db.paymentMethod (
    id bigint unsigned not null primary key,
    name varchar(255) not null,
    isActive bool not null,
    createdAt datetime not null,
    updatedAt datetime not null
    );

create table if not exists otus_cinema_db.payment (
    id bigint unsigned not null primary key,
    customerId bigint unsigned not null,
    status varchar(20) not null,
    externalId varchar(255) not null,
    paymentMethodId bigint unsigned not null,
    createdAt datetime not null,
    updatedAt datetime not null,
    foreign key (customerId) references customer(id),
    foreign key (paymentMethodId) references paymentMethod(id)
    );

create table if not exists otus_cinema_db.ticket (
    id bigint unsigned not null primary key,
    paymentId bigint unsigned not null,
    sessionId bigint unsigned not null,
    seatId bigint unsigned not null,
    status varchar(20) not null,
    createdAt datetime not null,
    updatedAt datetime not null,
    foreign key (paymentId) references payment(id),
    foreign key (sessionId) references session(id),
    foreign key (seatId) references seat(id)
    );
