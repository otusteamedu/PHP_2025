-- =======================================
CREATE
OR REPLACE FUNCTION public.clear_tables()
 RETURNS void
 LANGUAGE plpgsql
AS $function$
begin
TRUNCATE TABLE public.films RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.halls RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.seats RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.orders RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.sessions RESTART IDENTITY CASCADE;
TRUNCATE TABLE public.tickets RESTART IDENTITY CASCADE;
end;
$function$
;
-- =======================================
CREATE
OR REPLACE FUNCTION public.fill_films(num integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$
begin
INSERT INTO public.films ("name")
select concat('Фильм ', gs.id)
from generate_series(1, num) as gs(id);
end;
$function$
;
-- =======================================
CREATE
OR REPLACE FUNCTION public.fill_halls(num integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$
begin
INSERT INTO public.halls ("name")
select concat('Зал ', gs.id)
from generate_series(1, num) as gs(id);
end;
$function$
;
-- =======================================
CREATE
OR REPLACE FUNCTION public.fill_seats(num integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$
DECLARE
random_value NUMERIC(3, 1);
BEGIN
SELECT get_random_numeric_with_precision(1.1, 2.0)
INTO random_value;

INSERT INTO public.seats (hall_id, number_row, "number", coefficient)
SELECT gs.id   AS hall_id,
       data.number_row,
       data."number",
       CASE
           WHEN data.coefficient IS NULL THEN random_value
           ELSE data.coefficient
           END AS coefficient
FROM generate_series(1, num) AS gs(id),
     UNNEST(
             ARRAY[
                 (1, 1, 0::NUMERIC),
             (1, 2, NULL::NUMERIC),
             (1, 3, NULL::NUMERIC),
             (1, 4, 0::NUMERIC),
             (2, 5, 0::NUMERIC),
             (2, 6, NULL::NUMERIC),
             (2, 7, NULL::NUMERIC),
             (2, 8, 0::NUMERIC),
             (3, 9, 0::NUMERIC),
             (3, 10, 0::NUMERIC),
             (3, 11, 0::NUMERIC),
             (3, 12, 0::NUMERIC) ]
     ) AS data(number_row INT, "number" INT, coefficient NUMERIC);
END;
$function$
;
-- =======================================
-- Рандомная дата для сеансов
CREATE
OR REPLACE FUNCTION public.random_timestamp_session()
 RETURNS timestamp without time zone
 LANGUAGE plpgsql
AS $function$
begin
RETURN CURRENT_DATE - 365 + (random() * 365 * 2)::int +
       DATE_TRUNC('hour', TO_TIMESTAMP((60 * 60 * 10) + (RANDOM() * (60 * 60 * 14))) AT TIME ZONE 'UTC')::time;
end;
$function$
;
-- =======================================
-- Рандомное число
CREATE
OR REPLACE FUNCTION public.random_int(min_value integer, max_value integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$
BEGIN
RETURN random() * (max_value - min_value) + min_value;
END
$function$
;
-- =======================================
CREATE
OR REPLACE FUNCTION public.fill_sessions(num integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$
begin
INSERT INTO public.sessions ("start_at", "base_price", "hall_id", "film_id", "children_coefficient")
select random_timestamp_session(),
       random_int(100, 500),
       gs.id,
       gs.id,
       1
from generate_series(1, num) as gs(id);
end;
$function$
;
-- =======================================
CREATE
OR REPLACE FUNCTION public.get_random_date_before(input_date TIMESTAMP WITHOUT TIME ZONE)
RETURNS TIMESTAMP WITHOUT TIME ZONE
LANGUAGE plpgsql
AS $function$
DECLARE
start_date TIMESTAMP WITHOUT TIME ZONE;
    random_date
TIMESTAMP WITHOUT TIME ZONE;
BEGIN
    start_date
:= DATE_TRUNC('second', input_date - INTERVAL '20 days');

    IF
start_date > input_date THEN
        RETURN DATE_TRUNC('second', input_date);
END IF;

random_date
:= start_date + (RANDOM() * (input_date - start_date));
RETURN DATE_TRUNC('second', random_date);
END;
$function$
;
-- =======================================
CREATE
OR REPLACE FUNCTION public.fill_order_and_tickets(num integer)
RETURNS void
LANGUAGE plpgsql
AS $function$
BEGIN

INSERT INTO public.tickets ("session_id", "seat_id", "final_price")
SELECT s.id,
       st."number",
       (s.base_price + s.base_price * st.coefficient)
FROM sessions s
         JOIN seats st ON s.hall_id = st.hall_id
         CROSS JOIN generate_series(1, num) as gs(id)
WHERE s.film_id = gs.id;

INSERT INTO public.orders ("ticket_id", "sale_at")
SELECT t.id,
       get_random_date_before(s.start_at)
FROM tickets t
         JOIN sessions s ON s.id = t.session_id
         CROSS JOIN generate_series(1, num) as gs2(id)
WHERE s.film_id = gs2.id;
END;
$function$
;
-- =======================================
CREATE
OR REPLACE FUNCTION public.fill_all_data_10k()
RETURNS void
LANGUAGE plpgsql
AS $function$
       DECLARE
num INTEGER = 10000;
BEGIN
    PERFORM
public.clear_tables();
    PERFORM
public.fill_films(num);
    PERFORM
public.fill_halls(num);
    PERFORM
public.fill_seats(num);
    PERFORM
public.fill_sessions(num);
    PERFORM
public.fill_order_and_tickets(num);
END;
$function$
;
-- =======================================
CREATE
OR REPLACE FUNCTION public.fill_all_data_10kk()
RETURNS void
LANGUAGE plpgsql
AS $function$
       DECLARE
num INTEGER = 10000000;
BEGIN
    PERFORM
public.clear_tables();
    PERFORM
public.fill_films(num);
    PERFORM
public.fill_halls(num);
    PERFORM
public.fill_seats(num);
    PERFORM
public.fill_sessions(num);
    PERFORM
public.fill_order_and_tickets(num);
END;
$function$
;
-- =======================================
