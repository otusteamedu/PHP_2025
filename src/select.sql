-- фильм, задачи актуальные на сегодня, задачи актуальные через 20 дней
with premiere_is_today as (
    select id, movieId, dateVal from value as v
    where v.dateVal::date = CURRENT_DATE
    ),
    premiere_is_in_twenty_days as (
select id, movieId, dateVal from value as v
where v.dateVal::date = CURRENT_DATE+20
    )
select name, t.dateVal as today, t2.dateVal as through_twenty_days
from movie as m
         left join premiere_is_today as t on m.id = t.movieId
         left join premiere_is_in_twenty_days as t2 on m.id = t2.movieId
where t2.dateVal is not null or t.dateVal is not null
