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

-- Фильм, тип атрибута, атрибут, значение

with cost as (
	select movieId, 'Стоимость фильма в $' as attributeName, v.attributeId, CAST(moneyVal as text) as value from value as v
	where v.attributeId = (select id from attribute where name = 'Стоимость фильма в $')
),
director as (
	select movieId, 'Режиссёр' as attributeName, v.attributeId, textVal as value from value as v
	where v.attributeId = (select id from attribute where name = 'Режиссёр')
),
result as (
	select * from cost
	union all
	select * from director
)

select m.name, r.attributeName, t.name, r.value from result as r
left join movie as m on m.id = r.movieId
left join attribute_type as t on r.attributeId= t.id

group by m.name, attributename, t.name, r.value