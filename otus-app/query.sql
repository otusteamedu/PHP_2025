# служебные даты на сегодня/через 20 дней
CREATE OR REPLACE VIEW otus_cinema_eav.service_view as
select
    m.name as movieName,
    group_concat(if(av.dateValue = curdate(), a.name, null), ',') as todayTasks,
    group_concat(if(av.dateValue = date_add(curdate(), interval 20 day), a.name, null), ',') as in20DaysTasks
from otus_cinema_eav.movie m
         join otus_cinema_eav.attributeValue av on m.id = av.movieId
         join otus_cinema_eav.attribute a on a.id = av.attributeId
         join otus_cinema_eav.attributeType at on at.id = a.attributeTypeId
where at.name = 'служебные даты'
group by m.name
order by m.name;

# сборка данных для маркетинга
CREATE OR REPLACE VIEW otus_cinema_eav.marketing_view as
    select
        m.name as movieName,
        at.name as attributeTypeName,
        a.name as attributeName,
        case at.type
            when 'text' then av.textValue
            when 'string' then av.stringValue
            when 'bool' then if(av.boolValue = 1, 'yes', 'no')
            when 'date' then date_format(av.dateValue, '%d %M, %Y')
            when 'datetime' then date_format(av.datetimeValue, '%H:%i:%s %d %M, %Y')
            when 'int' then convert(av.intValue, char)
            when 'float' then convert(av.floatValue, char)
            else ''
    end as attributeValue
from otus_cinema_eav.movie m
join otus_cinema_eav.attributeValue av on m.id = av.movieId
join otus_cinema_eav.attribute a on a.id = av.attributeId
join otus_cinema_eav.attributeType at on at.id = a.attributeTypeId
order by movieName, attributeTypeName, attributeName;
