create table if not exists hall (
  id            bigserial primary key,
  name          varchar(100) not null,
  seat_rows     integer not null check (seat_rows > 0),
  seat_columns  integer not null check (seat_columns > 0),
  is_active     boolean not null default true
);

create table if not exists seat_type (
  id              bigserial primary key,
  code            varchar(50) not null unique,
  name            varchar(100) not null,
  price_modifier  numeric(8,2) not null default 0.00
);

create table if not exists ticket_status (
  code        varchar(16) primary key,
  name        varchar(100) not null
);

insert into ticket_status(code, name) values
  ('reserved','Reserved'),
  ('sold','Sold'),
  ('cancelled','Cancelled')
on conflict (code) do nothing;

create table if not exists seat (
  id             bigserial primary key,
  hall_id        bigint not null references hall(id) on delete restrict,
  row_number     integer not null check (row_number > 0),
  seat_number    integer not null check (seat_number > 0),
  seat_type_id   bigint not null references seat_type(id),
  unique (hall_id, row_number, seat_number)
);

create table if not exists movie (
  id                bigserial primary key,
  title             varchar(255) not null,
  duration_minutes  integer not null check (duration_minutes > 0),
  rating            varchar(20),
  release_date      date
);

create table if not exists showtime (
  id          bigserial primary key,
  hall_id     bigint not null references hall(id) on delete restrict,
  movie_id    bigint not null references movie(id) on delete restrict,
  starts_at   timestamptz not null,
  ends_at     timestamptz not null,
  base_price  numeric(8,2) not null check (base_price >= 0),
  unique (hall_id, starts_at),
  check (ends_at > starts_at)
);

CREATE EXTENSION IF NOT EXISTS citext;

create table if not exists customer (
  id          bigserial primary key,
  email       citext unique,
  phone       varchar(32),
  full_name   varchar(200),
  created_at  timestamptz not null default now()
);

create table if not exists ticket (
  id            bigserial primary key,
  showtime_id   bigint not null references showtime(id) on delete restrict,
  seat_id       bigint not null references seat(id) on delete restrict,
  status        varchar(16) not null references ticket_status(code) default 'reserved',
  price_paid    numeric(8,2),
  reserved_at   timestamptz not null default now(),
  sold_at       timestamptz,
  cancelled_at  timestamptz,
  customer_id   bigint references customer(id),
  unique (showtime_id, seat_id)
);




