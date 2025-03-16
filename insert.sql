-- таблица фильмов
INSERT INTO public.film(id, title, duration, price)
SELECT gen_random_uuid()                                                                   AS id,
       'Film ' || substr(md5(random()::text), 1, 10) || ' - ' || trunc(random() * 1000000) AS title,
       floor(random() * 240 * 60)                                                          AS duration,
       ceil(random() * 100) + 150                                                          AS price
from generate_series(1, 100);

-- таблица залов
INSERT INTO public.cinema_room (id, title, rating)
SELECT gen_random_uuid()     AS id,
       (ARRAY ['Blue', 'Green', 'Yellow', 'Red'])[floor(random() * 4 + 1)] || '_' ||
       (ARRAY ['VIP', 'Econom', 'Standard', 'Premium'])[floor(random() * 4 + 1)] || '_' ||
       floor(random() * 100) AS title,
       random() + 1          AS rating
from generate_series(1, 25)
;

-- таблица рейтинга сеансов
INSERT INTO public.session_rating (id, rating)
SELECT gen_random_uuid() AS id,
       random() + 1      AS rating
from generate_series(1, 25)
;

-- таблица сеансов
DO
$$
    DECLARE
        cinema_room      record;
        start_datetime   timestamp;
        end_datetime     timestamp;
        current_datetime timestamp;
        rating           record;
        film             record;
        minutes          varchar;
    BEGIN
        start_datetime := timestamp '2025-03-01 09:00:00';
        end_datetime := start_datetime + INTERVAL '10 days';
        WHILE end_datetime >= start_datetime
            LOOP
                FOR cinema_room IN
                    SELECT *
                    FROM public.cinema_room
                    LOOP
                        start_datetime := start_datetime;
                        current_datetime := start_datetime;
                        LOOP
                            SELECT * INTO film FROM film ORDER BY RANDOM() LIMIT 1;
                            SELECT * INTO rating FROM session_rating ORDER BY RANDOM() LIMIT 1;
                            minutes := round(film.duration / 60, 0) + 30 || ' minutes';
                            INSERT INTO public.session (id, start_from, end_to, film_id, rating_id, cinema_room_id)
                            VALUES (gen_random_uuid(), current_datetime, current_datetime + minutes::interval, film.id,
                                    rating.id, cinema_room.id);
                            current_datetime := current_datetime + minutes::interval + interval '15 minutes';
                            EXIT WHEN current_datetime >= start_datetime + INTERVAL '15 hours';
                        END LOOP;
                    END LOOP;
                start_datetime := start_datetime + INTERVAL '1 day';
            END LOOP;
    END;
$$;
