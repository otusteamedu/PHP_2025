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


set my.movie_count = 10000; -- 10000000
set my.cinema_count = 10000; -- 10000000
set my.customer_count = 10000; -- 10000000
set my.halls_count = 2;
set my.session_count = 10000; -- 10000000
set my.order_count = 10000; -- 10000000
set my.ticket_count = 10000; -- 10000000

insert into cinema.movie(name)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_100')::int)::int)
    from generate_series(1,current_setting('my.movie_count')::int) as gs(id);

insert into cinema.cinema(city, address)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * 200)::int)
    from generate_series(1,current_setting('my.cinema_count')::int) as gs(id);

insert into cinema.customer(name, email, phone)
    select
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_50')::int)::int),
        random_string(1 + floor(random() * current_setting('my.max_chars_varying_20')::int)::int)
    from generate_series(1, current_setting('my.customer_count')::int) as gs(id);

insert into cinema.hall(cinema_id, number)
    select c.id, h.num
    from (
             select
                 id,
                 current_setting('my.halls_count')::int as hall_count
             from cinema.cinema
         ) as c
             join lateral generate_series(1, current_setting('my.halls_count')::int) as h(num) on true
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

with time_slots as (
    select ts
    from generate_series(
         timestamp '2026-02-20 00:00:00',
         timestamp '2027-07-20 23:00:00',
         interval '1 hour'
     ) ts
)
insert into cinema.session(movie_id, hall_id, start_time, part_of_day)
select
    m.id,
    h.id,
    t.ts,
    case
        when extract(hour from t.ts) >= 4  and extract(hour from t.ts) < 9  then 'утро'::cinema.part_of_day
        when extract(hour from t.ts) >= 9  and extract(hour from t.ts) < 14 then 'день'::cinema.part_of_day
        when extract(hour from t.ts) >= 14 and extract(hour from t.ts) < 20 then 'вечер'::cinema.part_of_day
        else 'ночь'::cinema.part_of_day
        end
from generate_series(1, current_setting('my.session_count')::int) g(i)
join lateral (
    select id
    from cinema.movie
    order by random() + g.i * 0
    limit 1
) m on true

join lateral (
    select id
    from cinema.hall
    order by random() + g.i * 0
    limit 1
) h on true

join lateral (
    select ts
    from time_slots
    order by random() + g.i * 0
    limit 1
) t on true;

insert into cinema.price(session_id, seat_category, price)
    select
        s.id,
        c.category,
        (
            case s.part_of_day
                when 'утро'  then 200
                when 'день'  then 300
                when 'вечер' then 400
                when 'ночь'  then 250
            end
            +
            case c.category
                when 'с краю' then 0
                when 'с краю далеко' then 50
                when 'в центре, далеко' then 100
                when 'в центре' then 150
            end
        )::money

    from cinema.session s

    cross join (
        select unnest(enum_range(null::cinema.seat_category)) as category
    ) c;

insert into cinema.orders(customer_id, created_at)
    select
        c.id,
        timestamp '2026-03-01'
            + random() * (timestamp '2026-03-01' - timestamp '2026-01-01')
    from cinema.customer c
        limit current_setting('my.order_count')::int;

with candidates as (select s.id  as session_id,
                           s.hall_id,
                           pl.id as place_id,
                           pl.seat_category
                    from cinema.session s
                             join cinema.place pl on pl.hall_id = s.hall_id),
     free_places as (select *
                     from candidates c
                     where not exists (select 1
                                       from cinema.ticket ti
                                       where ti.session_id = c.session_id
                                         and ti.place_id = c.place_id)),
     randomized as (select *
                    from free_places
                    order by random()
    limit current_setting('my.ticket_count'):: int
    )
insert
into cinema.ticket(session_id, hall_id, order_id, place_id, price)
select r.session_id,
       r.hall_id,
       o.id,
       r.place_id,
       pr.price
from randomized r

         join lateral (
    select id
    from cinema.orders
    order by random()
        limit 1
) o
on true
    join cinema.price pr
    on pr.session_id = r.session_id
    and pr.seat_category = r.seat_category;