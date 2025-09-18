create table theatre (
    theatre_id serial primary key,
    name varchar(200) not null,
    location text
);

create table room (
    room_id serial primary key,
    theatre_id integer not null references theatre(theatre_id),
    name varchar(100) not null
);

create table movie (
    movie_id serial primary key,
    movie_name varchar(255) not null,
    duration integer,
    movie_description text,
    release_date date,
    rating real
);

create table session (
    session_id serial primary key,
    room_id integer not null references room(room_id),
    movie_id integer not null references movie(movie_id),
    start_time timestamp not null,
    end_time timestamp not null,
    base_price decimal(10,2) not null
);

create table seat_type (
    seat_type_id serial primary key,
    name varchar(100) not null,
    price_modifier decimal(4,2) not null
);

create table seat (
    seat_id serial primary key,
    room_id integer not null references room(room_id),
    row_number integer not null,
    seat_number integer not null,
    seat_type_id integer not null references seat_type(seat_type_id),
    unique(room_id, row_number, seat_number)
);

create table "user" (
    user_id serial primary key,
    name varchar(255) not null,
    email varchar(255) UNIQUE,
    phone varchar(25) not null unique,
    registration_date timestamp not null
);

create table order_status (
    order_status_id serial primary key,
    status_name varchar(100) not null
);

create table "order" (
    order_id serial primary key,
    user_id integer not null references "user"(user_id),
    created_at timestamp not null,
    order_status_id integer not null references order_status(order_status_id),
    order_total_price decimal(10,2) not null
);

create table booking (
    booking_id serial primary key,
    order_id integer not null references "order"(order_id) on delete cascade,
    session_id integer not null references session(session_id),
    seat_id integer not null references seat(seat_id),
    booking_price decimal(10,2) not null,
    unique(session_id, seat_id)
);


