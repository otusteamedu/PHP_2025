truncate table room restart identity cascade;

truncate table film restart identity cascade;

truncate table client restart identity cascade;

truncate table ticket restart identity cascade;

-- ~10_000_000 rows ---------------------------

-- room ----------------------------------          100 rooms
INSERT INTO
    room (id)
SELECT
    generate_series (1, 100);
-- room place ----------------------------          30_000 room place = 10 * 30 * rooms
INSERT INTO
    room_place (id_room, number_row, number_place, extra_price)
SELECT
    gs.id AS id_room,
    number_row,
    number_place,
    get_random_extra_price ()
FROM
    generate_series (1, 10) AS gs (id)
    CROSS JOIN generate_series (1, 10) AS number_row
    CROSS JOIN generate_series (1, 30) AS number_place;

-- film ----------------------------------          100 film
INSERT INTO
    film (name, price)
SELECT
    get_film (gs.id),
    get_random_price ()
FROM
    generate_series (1, 101) AS gs (id); 

-- film session --------------------------          100_000 film session
INSERT INTO
    film_session (id_room, id_film, date_start, date_end)
SELECT
    gs.id % 10 +1,
    get_random_film () +1,
    rd.date_start,
    rd.date_start + INTERVAL '2 hours' AS date_end
FROM
    generate_series (1, 100000) AS gs (id)
    CROSS JOIN LATERAL (
        SELECT
            get_random_datetime (gs.id) AS date_start
    ) AS rd;

-- client --------------------------                5_000_000 client = 50 * film_session
INSERT INTO
    client (date_buy)
SELECT
    ds.date_start - INTERVAL '1 hours'
FROM
    generate_series (1, 50)
    CROSS JOIN LATERAL (
        SELECT
            date_start
        FROM
            film_session
    ) AS ds;



-- ticket chunk --------------------------          5_000_000 ticket


INSERT INTO ticket (id_client, id_room_place, id_session, price)
SELECT
    cl.id,
    rp.id,
    fs.id,
    rp.extra_price * fs.price
FROM generate_series(1, 5000000) AS gs(id)         
CROSS JOIN generate_series(0,99) AS r(rm)
CROSS JOIN LATERAL
    (SELECT id
     FROM client
     OFFSET floor(random()*(SELECT count(*) FROM client)) LIMIT 1
    ) AS cl
CROSS JOIN LATERAL
    (SELECT fs.id, f.price
     FROM film_session fs
     JOIN film f ON f.id = fs.id_film
     WHERE fs.id_room = r.rm
     OFFSET floor(random()*
                 (SELECT count(*) FROM film_session WHERE id_room = r.rm))
     LIMIT 1
    ) AS fs
CROSS JOIN LATERAL
    (SELECT id, extra_price
     FROM room_place
     WHERE id_room = r.rm
     OFFSET floor(random()*
                 (SELECT count(*) FROM room_place WHERE id_room = r.rm))
     LIMIT 1
    ) AS rp;