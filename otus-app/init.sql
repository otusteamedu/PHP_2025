create table if not exists public.cinema (
    id serial primary key,
    name varchar(255) not null,
    info text not null,
    isActive bool not null,
    createdAt timestamp not null,
    updatedAt timestamp not null
    );

create table if not exists public.hall (
    id serial primary key,
    name varchar(255) not null,
    cinemaId int not null,
    createdAt timestamp not null,
    updatedAt timestamp not null,
    foreign key (cinemaId) references cinema(id)
    );

create table if not exists public.movie (
    id serial primary key,
    name varchar(255) not null,
    duration int not null,
    age int not null,
    createdAt timestamp not null,
    updatedAt timestamp not null
    );

-- defaultPrice is a * 100 value, f.e. 104050 = 1040.50
create table if not exists public.session (
    id serial primary key,
    hallId int not null,
    movieId int not null,
    defaultPrice bigint not null,
    startsAt timestamp not null,
    createdAt timestamp not null,
    updatedAt timestamp not null,
    foreign key (hallId) references hall(id),
    foreign key (movieId) references movie(id)
    );

-- priceModifier is a percent value, f.e. 120 = 120% = x1.2
create table if not exists public.seatType (
    id serial primary key,
    name varchar(255) not null,
    priceModifier int not null,
    createdAt timestamp not null,
    updatedAt timestamp not null
    );

create table if not exists public.seat (
    id serial primary key,
    seatTypeId int not null,
    hallId int not null,
    coordinates json not null,
    number varchar(20) not null,
    createdAt timestamp not null,
    updatedAt timestamp not null,
    foreign key (seatTypeId) references seatType(id),
    foreign key (hallId) references hall(id)
    );

create table if not exists public.customer (
    id serial primary key,
    email varchar(255) not null,
    birthDate date default null,
    name varchar(255) not null,
    password varchar(255) not null,
    createdAt timestamp not null,
    updatedAt timestamp not null
    );

create table if not exists public.paymentMethod (
    id serial primary key,
    name varchar(255) not null,
    isActive bool not null,
    createdAt timestamp not null,
    updatedAt timestamp not null
    );

create table if not exists public.payment (
    id serial primary key,
    customerId int not null,
    status varchar(20) not null,
    externalId varchar(255) not null,
    paymentMethodId int not null,
    createdAt timestamp not null,
    updatedAt timestamp not null,
    foreign key (customerId) references customer(id),
    foreign key (paymentMethodId) references paymentMethod(id)
    );

create table if not exists public.ticket (
    id serial primary key,
    paymentId int not null,
    sessionId int not null,
    seatId int not null,
    status varchar(20) not null,
    createdAt timestamp not null,
    updatedAt timestamp not null,
    foreign key (paymentId) references payment(id),
    foreign key (sessionId) references session(id),
    foreign key (seatId) references seat(id)
    );
