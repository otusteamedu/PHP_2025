-- Удаление представлений если существуют
DROP VIEW IF EXISTS service_view;
DROP VIEW IF EXISTS marketing_view;

- фильм, задачи актуальные на сегодня, задачи актуальные через 20 дней
- фильм, тип атрибута, атрибут, значение (значение выводим как текст)

-- View сборки служебных данных в форме
CREATE OR REPLACE VIEW service_view AS 
with today_tasks_list as 
(select e.entity_id, title, a.name as today_tasks FROM entites e
inner JOIN values v on v.entity_id = e.entity_id and CAST(v.value_datetime AS DATE)= CURRENT_DATE
LEFT JOIN "attributes" a ON a.attribute_id = v.attribute_id),
future_tasks_list as 
(select e.entity_id, title, a.name as future_tasks FROM entites e
inner JOIN values v on v.entity_id = e.entity_id and CAST(v.value_datetime AS DATE)= CURRENT_DATE + INTERVAL '20 days'
LEFT JOIN "attributes" a ON a.attribute_id = v.attribute_id)
SELECT title, today_tasks, null as future_tasks FROM today_tasks_list
union
SELECT title, null as today_tasks, future_tasks from future_tasks_list 
ORDER BY title;

select * from service_view;


-- View сборки данных для маркетинга в форме (три колонки)
CREATE OR REPLACE VIEW marketing_view AS
SELECT 
    e.title AS "Фильм",
    ta.name AS "Тип атрибута",
    a.name AS "Атрибут",
    CASE 
        WHEN ta.name = 'Текст' THEN v.value_string
        WHEN ta.name = 'Логический' THEN 
            CASE 
                WHEN v.value_bool = TRUE THEN 'Да'
                ELSE 'Нет'
            END
        WHEN ta.name = 'Дата/время' THEN TO_CHAR(v.value_datetime, 'DD.MM.YYYY HH24:MI')
        WHEN ta.name = 'Целое число' THEN CAST(v.value_int AS TEXT)
        WHEN ta.name = 'Число с плавающей точкой' THEN CAST(v.value_float AS TEXT)
    END AS "Значение"
FROM entites e
inner JOIN values v ON e.entity_id = v.entity_id
LEFT JOIN attributes a ON v.attribute_id = a.attribute_id
LEFT JOIN types_attributes ta ON a.type_id = ta.type_id
ORDER BY e.title, a.name;