-- Простые запросы

-- 1
explain select max(t.price), min(t.price)
from cinema.ticket as t
where t.session_id = 3258

-- 2
explain select t.place_id
from cinema.ticket as t
where t.session_id = 3258

-- 3
explain select hall_id as id
from cinema.session as s
where s.id = 3258

-- Сложные запросы

-- 1
                            explain with sessions as (
    select s.id as id, m.name as movie_name
    from cinema.session as s
             join cinema.movie as m on m.id = s.movie_id
    where s.start_time >= date_trunc('week', now())
      and  s.start_time < date_trunc('week', now()) + interval '1 week'
)
select
    s.movie_name,
    sum(t.price) as total_revenue
from sessions as s
         join cinema.ticket as t on s.id = t.session_id
group by s.movie_name
order by total_revenue desc

-- 2
explain select count(t.id)
from cinema.ticket as t
         join cinema.orders as o on o.id = t.order_id
where o.created_at >= date_trunc('week', now()) and  o.created_at < date_trunc('week', now()) + interval '1 week'


-- 3
explain with hall as (
    select hall_id as id from cinema.session as s where s.id = 3258
    ),
    places as (
    select row, seat, p.id
    from cinema.place as p
    join hall as h on p.hall_id = h.id
    ),
    busy_places as (
    select t.place_id
    from cinema.ticket as t
    where t.session_id = 3258
    ),
    seats as (
    select
    p.row,
    p.seat,
    case
    when b.place_id is null then 'o'
    else 'x'
    end as status
    from places p
    left join busy_places b on b.place_id = p.id
    )
select
    row,
    array_agg(status order by seat) as seats
from seats
group by row
order by row;