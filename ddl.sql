create type enum_films_limitation as enum ('0+', '6+', '12+', '16+', '18+');
create type enum_halls_type as enum ('vip', 'comfort', 'standart', '3D');
create type enum_places_type as enum ('economy', 'standart', 'comfort', 'vip');

-- Залы
create table halls
(
    id   serial primary key,
    type enum_halls_type,
    name varchar(255) unique
);

-- Места
create table places
(
    id         serial primary key,
    hall_id    int references halls (id),
    type       enum_places_type,
    row_number smallint CHECK (row_number >= 0),
    number     smallint CHECK (number >= 0),
    constraint unique_places_number_and_row_number unique (number, row_number)
);

-- Фильмы
create table films
(
    id         serial primary key,
    title      varchar(255) unique,
    raiting    decimal(4, 2) not null default '0',
    limitation enum_films_limitation
);

-- Пользователи
create table users
(
    id    serial primary key,
    fio   varchar(255),
    email varchar(255) unique,
    phone varchar(255)
);

-- Сеансы
create table sessions
(
    id         serial primary key,
    film_id    int references films (id),
    hall_id    int references halls (id),
    created_at timestamp with time zone default now()
);

-- Заказы
create table orders
(
    id         serial primary key,
    user_id    int references users (id),
    number     varchar(255) unique,
    status     varchar(255),
    price_max  decimal(10, 2) not null  default '0',
    price_min  decimal(10, 2) not null  default '0',
    created_at timestamp with time zone default now()
);

-- Билеты
create table tickets
(
    id         serial primary key,
    session_id int references sessions (id),
    place_id   int references places (id),
    order_id   int references orders (id),
    constraint unique_tickets_session_id_and_place_id unique (session_id, place_id)
);

-- Платежи
create table payments
(
    id         serial primary key,
    order_id   int references orders (id),
    status     varchar(255),
    amount     decimal(12, 2) not null  default '0',
    method     varchar(255),
    payed_at   timestamp null,
    created_at timestamp with time zone default now()
);

