truncate table cinema,customer,hall,movie,payment,paymentmethod,seat,seattype,session,ticket RESTART IDENTITY CASCADE;
create or replace function fill_dummy_data(num integer) returns void as
$$
BEGIN
    INSERT INTO public.cinema (id, name, info, isActive, createdAt, updatedAt)
    SELECT
        gs.id,
        concat('cinema', gs.id),
        concat('some info ', gs.id),
        1::bool,
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);

    INSERT INTO public.customer (id, email, birthDate, name, password, createdAt, updatedAt)
    SELECT
        gs.id,
        concat('email', gs.id, '@test.test'),
        current_date - ('1 week'::interval * (random() * (1000 - 800) + 800)::int),
        concat('username', gs.id),
        concat('pass', gs.id),
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);

    INSERT INTO public.movie (id, name, duration, age, createdAt, updatedAt)
    SELECT
        gs.id,
        concat('movie', gs.id),
        ((random() * (10800 - 3600)) + 3600)::int,
        (random() * 24)::int,
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);

    INSERT INTO public.paymentMethod (id, name, isActive, createdAt, updatedAt)
    SELECT
        gs.id,
        concat('method name', gs.id),
        (random())::int::bool,
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);

    INSERT INTO public.seatType (id, name, priceModifier, createdAt, updatedAt)
    SELECT
        gs.id,
        concat('type', gs.id),
        ((random() * (300 - 75)) + 75)::int,
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);

    INSERT INTO public.hall (id, name, cinemaId, createdAt, updatedAt)
    SELECT
        gs.id,
        concat('hall', gs.id),
        ((random() * (num - 1)) + 1)::int,
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);

    INSERT INTO public.payment (id, customerId, status, externalId, paymentMethodId, createdAt, updatedAt)
    SELECT
        gs.id,
        ((random() * (num - 1)) + 1)::int,
        (array['paid','pending','failed'])[floor(random() * 3 + 1)],
        concat('external id', gs.id),
        ((random() * (num - 1)) + 1)::int,
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);

    INSERT INTO public.seat (id, seatTypeId, hallId, coordinates, number, createdAt, updatedAt)
    SELECT
        gs.id,
        ((random() * (num - 1)) + 1)::int,
        ((random() * (num - 1)) + 1)::int,
        '{}',
        ((random() * (num - 1)) + 1)::int,
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id)
    where not exists(select 1 from public.seat where id = gs.id);

    INSERT INTO public.session (id, hallId, movieId, defaultPrice, startsAt, createdAt, updatedAt)
    SELECT
        gs.id,
        ((random() * (num - 1)) + 1)::int,
        ((random() * (num - 1)) + 1)::int,
        ((random() * (500000 - 10000)) + 10000)::int,
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);

    INSERT INTO public.ticket (id, paymentId, sessionId, seatId, status, createdAt, updatedAt)
    SELECT
        gs.id,
        ((random() * (num - 1)) + 1)::int,
        ((random() * (num - 1)) + 1)::int,
        ((random() * (num - 1)) + 1)::int,
        (array['paid','booked','free'])[floor(random() * 3 + 1)],
        now() - ('1 year'::interval * random()),
        now() - ('1 year'::interval * random())
    FROM generate_series(1, num) as gs(id);
END;
$$ language plpgsql;

-- select fill_dummy_data(10000);
select fill_dummy_data(1000000);