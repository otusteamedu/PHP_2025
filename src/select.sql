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
country as (
	select movieId, 'Страна' as attributeName, v.attributeId, textVal as value from value as v
	where v.attributeId = (select id from attribute where name = 'Страна')
),
genre as (
	select movieId, 'Жанр' as attributeName, v.attributeId, textVal as value from value as v
	where v.attributeId = (select id from attribute where name = 'Жанр')
),
duration as (
	select movieId, 'Длительность в минутах' as attributeName, v.attributeId, CAST(integerVal as text) as value from value as v
	where v.attributeId = (select id from attribute where name = 'Длительность в минутах')
),
rating as (
	select movieId, 'Рейтинг' as attributeName, v.attributeId, CAST(realVal as text) as value from value as v
	where v.attributeId = (select id from attribute where name = 'Рейтинг')
),
year_of_release as (
	select movieId, 'Год выпуска' as attributeName, v.attributeId, CAST(integerVal as text) as value from value as v
	where v.attributeId = (select id from attribute where name = 'Год выпуска')
),
premiere_date as (
	select movieId, 'Дата премьеры' as attributeName, v.attributeId, CAST(dateVal as text) as value from value as v
	where v.attributeId = (select id from attribute where name = 'Дата премьеры')
),
oscar as (
	select movieId, 'Оскар' as attributeName, v.attributeId, CAST(booleanVal as text) as value from value as v
	where v.attributeId = (select id from attribute where name = 'Оскар')
),

result as (
	select * from cost
	union all
	select * from director
	union all
	select * from country
	union all
	select * from genre
	union all
	select * from duration
	union all
	select * from rating
	union all
	select * from year_of_release
	union all
	select * from premiere_date
	union all
	select * from oscar
)

select m.name, r.attributeName, t.name, r.value from result as r
left join movie as m on m.id = r.movieId
left join (
    select a.id, t.name from attribute as a
        join attribute_type as t on a.typeId = t.id
    ) as t
    on r.attributeId= t.id

group by m.name, attributename, t.name, r.value