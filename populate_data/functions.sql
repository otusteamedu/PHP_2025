create or replace function random_string(length integer) returns text as
$$
declare
  chars text[] := '{0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z}';
  result text := '';
  i integer := 0;
begin
  if length < 0 then
    raise exception 'Given length cannot be less than 0';
  end if;
  for i in 1..length loop
    result := result || chars[1+random()*(array_length(chars, 1)-1)];
  end loop;
  return result;
end;
$$ language plpgsql;


create or replace function fill_seat_types_order_status() returns void as
$$
begin 

insert into seat_type (name, price_modifier) values
('Обычный', 1.00),
('Комфорт', 1.30),
('VIP', 1.80),
('Люкс', 2.20);

insert into "order_status" ("status_name") values
('Создан'),
('Оплачен'),
('Отменен'),
('Выполнен');

end;
$$ language plpgsql;

create or replace function fill_movies(num integer) returns void as
$$
begin 
insert into movie (movie_name, duration, movie_description, release_date, rating)
select
    random_string(20),
    round(20 + (random() * 80)),
    random_string(30),
    date '2020-01-01' - (random() * 700)::integer,
    round((random() * 10))
from generate_series(1, num);
end;
$$ language plpgsql;


--Театры
create or replace function fill_theatres(num integer) returns void as 
$$ 
begin 
insert into theatre (name, location)
select 
    random_string(20),
    random_string(20)
from generate_series(1, num);
end;
$$ language plpgsql;

-- Залы
create or replace function fill_rooms(num integer) returns void as
$$
begin
insert into room (theatre_id, name)
select 
    gs.id,
    random_string(30)
from 
    generate_series(1, num) as gs(id);
end
$$ language plpgsql;


-- Места в залах
create or replace function fill_seats(num integer, divider decimal) returns void as
$$
begin

insert into seat(seat_id, room_id, row_number, seat_number, seat_type_id)
select 
    gs.id,
    1 + ((gs.id - 1) / divider::integer),  -- Корректное распределение по комнатам
    1 + ((gs.id - 1) / 10),  -- Ряды по 10 мест
    1 + ((gs.id - 1) % 10),  -- Номера мест 1-10
    1 + floor(random() * 4)::integer  -- seat_type_id от 1 до 4
from
    generate_series(1, num) as gs(id);
    
end
$$ language plpgsql;


-- Заполняем юзверей
create or replace function fill_users(num integer) returns void as
$$
begin

insert into "user" (user_id, name, email, phone, registration_date)
select 
       gs.id,
       random_string(30),
       random_string(20) || '@' || random_string(5) || '.com',
       '+79' || floor(random() * (10000000000 - 1000000 + 1) + 1000000)::bigint,
       date '2020-01-01' + (random() * 700)::integer
from generate_series(1, num) as gs(id)
on conflict do nothing;

end
$$ language plpgsql;


-- Заполняем показы
create or replace function fill_sessions(num integer, divider numeric) returns void as
$$
begin


insert into "session"(session_id, room_id, movie_id, start_time, end_time, base_price)
select 
    gs.id,
    1 + ((gs.id - 1) / divider::integer),
    1 + floor(random() * (select count(*) from movie))::integer,
    now() - (random() * INTERVAL '60 days'),
    now() - (random() * INTERVAL '60 days') + interval '2 hours', -- Пример: +2 часа
    round(400 + (random() * (1000 - 400)))
from 
    generate_series(1, num) as gs(id);
end
$$ language plpgsql;


-- Заполняем зазазы
create or replace function fill_orders(num integer, divider numeric) returns void as
$$
begin

insert into "order"(order_id, user_id, created_at, order_status_id, order_total_price)
select 
    gs.id,
    ceil(gs.id / divider),
    now() - (random() * INTERVAL '20 days'),
    round(1 + random() * 3),
    round(1200 + (random() * (2000 - 1200)))
from 
    generate_series(1, num) as gs(id);
end
$$ language plpgsql;


create or replace function fill_bookings(num integer, divider decimal) returns void as
$$

begin

insert into booking(order_id, session_id, seat_id, booking_price)
select 
	1 + round(random() * "session".session_id),
	"session".session_id,
	seat.seat_id,
    round(400 + (random() * (1000 - 400))) + (num / divider / "session".session_id)

	from seat
	right join "session" on "session".room_id = seat.room_id
	order by random()
	limit num
    on conflict(session_id, seat_id) do nothing;
end
$$ language plpgsql;


create or replace function fill_database(num integer) returns void as
$$
DECLARE
  divider numeric;
begin
truncate table theatre, room, movie, "session", seat_type, seat, "user", order_status, "order", booking restart identity cascade;

divider := 1000.0;

alter table booking set unlogged;
alter table "order" set unlogged;
alter table "user" set unlogged;
alter table seat set unlogged;
alter table seat_type set unlogged;
alter table "session" set unlogged;
alter table order_status set unlogged;
alter table room set unlogged;
alter table theatre set unlogged;
alter table "order" set unlogged;
alter table movie set unlogged;
        PERFORM fill_seat_types_order_status();
        PERFORM fill_theatres(round(num / divider)::integer);
        PERFORM fill_rooms(round(num / divider)::integer);
        PERFORM fill_movies(num);
        PERFORM fill_seats(num, divider);
        PERFORM fill_users(round(num / divider)::integer);
       -- PERFORM fill_users(round(num / 100::numeric)::integer);
        PERFORM fill_sessions(num, divider);
        PERFORM fill_orders(num, divider);
        PERFORM fill_bookings(num, divider);
alter table movie SET LOGGED;
alter table "user" SET LOGGED;
alter table order_status SET LOGGED;
alter table "order" SET LOGGED;
alter table theatre SET LOGGED;
alter table room SET LOGGED;
alter table "session" SET LOGGED;
alter table seat_type SET LOGGED;
alter table seat SET LOGGED;
alter table "order" SET LOGGED;
alter table booking SET LOGGED;

end;
$$ language plpgsql;


-- Заполняем 10000 значений
-- select fill_database(10000);

-- Заполняем 10000000 значений
-- select fill_database(10000000);