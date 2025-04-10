--Генерация случайной строки----
Create or replace function random_string(length integer) returns text as
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

--Генерация случайного числа----
create or replace function random_int(min_value integer, max_value integer) returns integer as
$$
BEGIN
RETURN random() * (max_value - min_value) + min_value;
END
$$ language plpgsql;

--Генерация случайной даты----
create or replace function random_timestamp() returns timestamp as
$$
BEGIN
RETURN CURRENT_DATE - 365 + (random() * 365 * 2)::int
           + date_trunc('hour',('10:00'::time + (random() * ('23:00'::time - '10:00'::time)))::time);
END;
$$ language plpgsql;


-- Заполненяем клиентов
create or replace function fill_clients(num integer) returns void as
$$
BEGIN
INSERT INTO "clients" ("fio", "phone")
SELECT
    CONCAT(random_string(10),' ',random_string(5),' ',random_string(10),
    CONCAT('+79',random_int(000000000,999999999));
FROM generate_series(1, num);
END;
$$ language plpgsql;

-- Заполняем заказы
create or replace function fill_orders(num integer) returns void as
$$
DECLARE
    max_client_id int := (SELECT max(id) FROM clients);
    orders_statuses text[] := ARRAY['pending','paid','cancelled'];
BEGIN
INSERT INTO "orders" ("id", "client_id", "created_at", "status")
SELECT
    ss.id,
    random_int(1, max_client_id),
    random_timestamp(),
    (orders_statuses[random_int(1,3)::INT])
FROM generate_series(1, num) as ss(id);
END;
$$ language plpgsql;

-- Заполняем фильмы
create or replace function fill_films(num integer) returns void as
$$
BEGIN
INSERT INTO "films" ("title")
SELECT
    random_string(50)
FROM generate_series(1, num);
END;
$$ language plpgsql;

-- Заполняем залы
create or replace function fill_rooms(num integer) returns void as
$$
BEGIN
INSERT INTO "rooms" ("cinema_id","name")
SELECT
    1,
    random_string(50)
FROM generate_series(1, num);
END;
$$ language plpgsql;

-- Заполняем сеансы
create or replace function fill_sessions(num integer) returns void as
$$
DECLARE
    max_film_id int := (SELECT max(id) FROM films);
    max_room_id int := (SELECT max(id) FROM rooms);
BEGIN
INSERT INTO "session" ("film_id", "room_id", "start_time","base_price")
SELECT
    random_int(1, max_film_id),
    random_int(1, max_room_id),
    random_timestamp(),
    random_int(400, 1200)::numeric(10,2)
FROM generate_series(1, num);
END;
$$ language plpgsql;


-- Заполняем билеты
create or replace function fill_tickets(num integer) returns void as
$$
DECLARE
    max_order_id int := (SELECT max(id) FROM orders);
    max_session_id int := (SELECT max(id) FROM session);
    max_room_id int := (SELECT max(id) FROM rooms);
BEGIN
INSERT INTO "tickets" ("session_id", "order_id", "seat_number", "row_number", "price")
SELECT
    random_int(1, max_session_id),
    CASE
        WHEN random() < 0.1 THEN NULL  -- 10% вероятности для NULL
        ELSE random_int(1, max_order_id)
        END,
    random_int(1, 800),
    random_int(1, 300),
    random_int(400,1200)::numeric(10, 2)
FROM generate_series(1, num);
END;
$$ language plpgsql;

create or replace function fill_database_test(counts integer) returns void as
$$
DECLARE
seq RECORD;
BEGIN
    TRUNCATE TABLE clients, films, orders, rooms, session, tickets CASCADE;

    FOR seq IN
    SELECT sequence_name
    FROM information_schema.sequences
    WHERE sequence_schema = 'public'  -- если последовательности находятся в схеме public
        LOOP
            EXECUTE 'ALTER SEQUENCE ' || seq.sequence_name || ' RESTART WITH 1';
    END LOOP;

BEGIN;
ALTER TABLE tickets SET UNLOGGED;
ALTER TABLE orders SET UNLOGGED;
ALTER TABLE clients SET UNLOGGED;
ALTER TABLE session SET UNLOGGED;
ALTER TABLE films SET UNLOGGED;
ALTER TABLE rooms SET UNLOGGED;
        PERFORM fill_clients(counts);
        PERFORM fill_orders(counts);
        PERFORM fill_films(counts);
        PERFORM fill_rooms(counts);
        PERFORM fill_sessions(counts);
        PERFORM fill_tickets(counts);
ALTER TABLE clients SET LOGGED;
ALTER TABLE films SET LOGGED;
ALTER TABLE orders SET LOGGED;
ALTER TABLE rooms SET LOGGED;
ALTER TABLE session SET LOGGED;
ALTER TABLE tickets SET LOGGED;
COMMIT;

END;
$$ language plpgsql;