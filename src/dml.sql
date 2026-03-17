--
Create or replace function random_string(length integer) returns text as
$$
declare
chars text[] := '{0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z}';
    result text := '';
    i integer := 0;
begin
    if length < 0 then
        raise exception 'Given length cannot be less than 0!';
end if;
for i in 1..length loop
        result := result || chars[1 + random() * (array_length(chars, 1) - 1)];
end loop;
return result;
end;
$$ language plpgsql;

set lc_monetary = "ru_RU.UTF-8";
set my.max_chars_varying_20 = 20;
set my.max_chars_varying_50 = 50;
set my.max_chars_varying_100 = 100;

set my.amount_data_to_generate_smallserial = 32; -- 32000
set my.amount_data_to_generate_serial = 10000; -- 10000000
set my.amount_data_to_generate_bigserial = 10000; -- 10000000

set my.max_count_halls = 30;

insert into cinema.movie(name)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_100')::int)::int)
    from generate_series(1,current_setting('my.amount_data_to_generate_bigserial')::int) as gs(id);

insert into cinema.cinema(city, address)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * 200)::int)
    from generate_series(1,current_setting('my.amount_data_to_generate_smallserial')::int) as gs(id);

insert into cinema.customer(name, email, phone)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_20')::int)::int)
    from generate_series(1, current_setting('my.amount_data_to_generate_bigserial')::int) as gs(id);

insert into cinema.hall(cinemaId, number)
    select c.id, h.num
    from (
             select
                 id,
                 floor(
                     random()
                         * current_setting('my.max_count_halls')::int
                 )::int + 1 as hall_count
             from cinema.cinema
         ) as c
             join lateral generate_series(1, c.hall_count) as h(num) on true
    order by c.id, h.num;

insert into cinema.place (hall_id, row, seat, seat_category)
    select
        h.id as hall_id,
        r.row_number as row,
        s.seat_number as seat,
        c.category as category
    from cinema.hall as h

         cross join generate_series(1, 10) as r(row_number)
         cross join generate_series(1, 30) as s(seat_number)
         cross join lateral (
    select
        case
            when s.seat_number between 3 and 5 then 'с краю далеко'::cinema.seat_category
            when s.seat_number between 6 and 9 then 'в центре, далеко'::cinema.seat_category
            when s.seat_number between 10 and 20 then 'в центре'::cinema.seat_category
            when s.seat_number between 21 and 24 then 'в центре, далеко'::cinema.seat_category
            when s.seat_number between 25 and 28 then 'с краю далеко'::cinema.seat_category
            else 'с краю'::cinema.seat_category
        end) as c(category);

insert into cinema.session(movie_id, hall_id, start_time, part_of_day)
select
    m.id,
    h.id,
    ts,
    case
        when extract(hour from ts) >= 4  and extract(hour from ts) < 9  then 'утро'::cinema.part_of_day
        when extract(hour from ts) >= 9  and extract(hour from ts) < 14 then 'день'::cinema.part_of_day
        when extract(hour from ts) >= 14 and extract(hour from ts) < 20 then 'вечер'::cinema.part_of_day
        else 'ночь'::cinema.part_of_day
        end

from cinema.movie m
join cinema.hall h on true
cross join lateral (
    generate_series(
        timestamp '2020-01-10 00:00:00',
        timestamp '2026-07-20 23:00:00',
        interval '1 hour'
    ) as ts(ts)
limit 10000;


set my.minimum_price_range = 200;
set my.maximum_price_range = 600;
set my.place_category_count = 4;

set my.step_price = 50;
set my.min_price = 200;
-- 600 - 3 * 5
set my.max_price = 450;

with settings as (
    select
        current_setting('my.minimum_price_range')::int as min_price,
            current_setting('my.maximum_price_range')::int as max_price,
            current_setting('my.place_category_count')::int as count_categories,
            current_setting('my.step_price')::int as step_price
),

base_prices as (
    -- для каждой пары (movie, partOfDay) выбираем одну базовую цену
    select
     m.id as movie_id,
     p.partOfDay,
     (
         floor( random()
                     * (
                          (s.max_price - (s.step_price * (s.count_categories - 1)))
                          - s.min_price
                      + 1
                  )
              )::int
             + s.min_price

     ) as base_price
    from cinema.movie m
    cross join (
        select unnest(enum_range(null::partOfDay)) as partOfDay
        ) p
    cross join settings as s
),
inserted_prices as (
    insert into cinema.price (movieid, partofday, price)
    select
        bp.movie_id,
        bp.partofday,
        (bp.base_price + (g.step * 50))::money as price
    from base_prices as bp
    cross join settings as s
    cross join generate_series(0, s.count_categories - 1) as g(step)

    returning id, movieId, partOfDay, price
),
places as (
    select
        h.id as hall_id,
        r.row_number as row,
        pl.place_number as place,
        c.category as category
    from cinema.hall as h

    cross join generate_series(1, 10) as r(row_number)
    cross join generate_series(1, 30) as pl(place_number)
    cross join lateral (
        select
            case
                when pl.place_number between 3 and 5 then 'С краю далеко'::placecategory
                when pl.place_number between 6 and 9 then 'В центре, далеко'::placecategory
                when pl.place_number between 10 and 20 then 'В центре'::placecategory
                when pl.place_number between 21 and 24 then 'В центре, далеко'::placecategory
                when pl.place_number between 25 and 28 then 'С краю далеко'::placecategory
            else 'С краю'::placecategory
            end as category
    ) c;
)
select * from places
