create or replace function fill_database_test_10k() returns void as
$$
BEGIN
    perform fill_clients(10000);
    perform fill_orders(10000);
    perform fill_movies(10000);
    perform fill_halls(100);
    perform fill_seat_categories();
    perform fill_sessions(10000);
    perform fill_seat_prices(10000, 400, 1000);
    perform fill_seats(10000, 20, 30);
    perform fill_tickets(10000, 400, 1000);
END;
$$ language plpgsql;

create or replace function fill_database_test_10kk() returns void as
$$
BEGIN
    perform fill_clients(5000000);
    perform fill_orders(5000000);
    perform fill_movies(5000000);
    perform fill_halls(10000);
    perform fill_seat_categories();
    perform fill_sessions(5000000);
    perform fill_seat_prices(5000000, 400, 1000);
    perform fill_seats(5000000, 20, 30);
    perform fill_tickets(5000000, 400, 1000);
END;
$$ language plpgsql;

-- Fill clients
create or replace function fill_clients(num integer) returns void as
$$
BEGIN
    INSERT INTO "clients" ("id", "email", "phone", "last_name", "first_name", "middle_name")
    SELECT
        gs.id,
        random_string(20),
        CONCAT(random_string(10), '@mail.local'),
        random_string(20),
        random_string(15),
        random_string(10)
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- Fill orders
create or replace function fill_orders(num integer) returns void as
$$
DECLARE
    max_client_id int := (SELECT max(id) FROM clients);
    orders_statuses text[] := '[0:2]={new,processing,paid}'::text[];
BEGIN
    INSERT INTO "orders" ("id", "client_id", "paid_at", "status")
    SELECT
        gs.id,
        random_int(1, max_client_id),
        random_timestamp(),
        (orders_statuses[trunc(random() * 3)::int])::order_status
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- Fill movies
create or replace function fill_movies(num integer) returns void as
$$
DECLARE
    age_limits int[] := '[0:4]={0,6,12,16,18}'::int[];
BEGIN
    INSERT INTO "movies" ("id", "name", "duration", "age_limit")
    SELECT
        gs.id,
        random_string(50),
        random_int(90, 240),
        age_limits[trunc(random() * 5)::int]
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- Fill halls
create or replace function fill_halls(num integer) returns void as
$$
BEGIN
    INSERT INTO "halls" ("id", "name")
    SELECT
        gs.id,
        random_string(50)
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- Fill seat categories
create or replace function fill_seat_categories() returns void as
$$
BEGIN
    INSERT INTO "seat_categories" ("id", "name")
    VALUES
        (1, 'Standard'),
        (2, 'Comfort'),
        (3, 'VIP');
END;
$$ language plpgsql;

-- Fill sessions
create or replace function fill_sessions(num integer) returns void as
$$
DECLARE
    max_movie_id int := (SELECT max(id) FROM movies);
    max_hall_id int := (SELECT max(id) FROM halls);
BEGIN
    INSERT INTO "sessions" ("id", "movie_id", "hall_id", "started_at")
    SELECT
        gs.id,
        random_int(1, max_movie_id),
        random_int(1, max_hall_id),
        random_timestamp()
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- Fill seat prices
create or replace function fill_seat_prices(num integer, min_price integer, max_price integer) returns void as
$$
DECLARE
    max_session_id int := (SELECT max(id) FROM sessions);
    max_seat_category_id int := (SELECT max(id) FROM seat_categories);
BEGIN
    INSERT INTO "seat_prices" ("id", "session_id", "seat_category_id", "price")
    SELECT
        gs.id,
        random_int(1, max_session_id),
        random_int(1, max_seat_category_id),
        random_int(min_price, max_price)::numeric(10, 2)
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- Fill seats
create or replace function fill_seats(num integer, row_num integer, seat_number_num integer) returns void as
$$
DECLARE
    max_seat_category_id int := (SELECT max(id) FROM seat_categories);
    max_hall_id int := (SELECT max(id) FROM halls);
BEGIN
    INSERT INTO "seats" ("id", "seat_category_id", "hall_id", "row", "seat_number")
    SELECT
        gs.id,
        random_int(1, max_seat_category_id),
        random_int(1, max_hall_id),
        random_int(1, row_num),
        random_int(1, seat_number_num)
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- Fill tickets
create or replace function fill_tickets(num integer, min_price integer, max_price integer) returns void as
$$
DECLARE
    max_order_id int := (SELECT max(id) FROM orders);
    max_session_id int := (SELECT max(id) FROM sessions);
    max_seat_id int := (SELECT max(id) FROM seats);
BEGIN
    INSERT INTO "tickets" ("id", "order_id", "session_id", "seat_id", "price")
    SELECT
        gs.id,
        random_int(1, max_order_id),
        random_int(1, max_session_id),
        random_int(1, max_seat_id),
        random_int(min_price, max_price)::numeric(10, 2)
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- Generate random string
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

-- Generate random int
create or replace function random_int(min_value integer, max_value integer) returns integer as
$$
BEGIN
    RETURN random() * (max_value - min_value) + min_value;
END
$$ language plpgsql;

-- Generate random timestamp
create or replace function random_timestamp() returns timestamp as
$$
BEGIN
    RETURN CURRENT_DATE - 365 + (random() * 365 * 2)::int
           + date_trunc('hour',('10:00'::time + (random() * ('23:00'::time - '10:00'::time)))::time);
END;
$$ language plpgsql;
