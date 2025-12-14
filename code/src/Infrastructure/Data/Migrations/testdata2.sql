-- Сначала вставляем типы атрибутов
INSERT INTO types_attributes (name) VALUES 
    ('Текст'),
    ('Логический'),
    ('Дата/время'),
    ('Целое число'),
    ('Число с плавающей точкой')
ON CONFLICT (name) DO NOTHING;

-- Теперь вставляем атрибуты с указанием type_id
INSERT INTO attributes (name, type_id) 
SELECT 
    attr_name,
    (SELECT type_id FROM types_attributes WHERE name = type_name) as type_id
FROM (VALUES 
    ('Рецензия', 'Текст'),
    ('Оскар', 'Логический'),
    ('Ника', 'Логический'),
    ('Мировая премьера', 'Дата/время'),
    ('Премьера в РФ', 'Дата/время'),
    ('Дата начала продажи билетов', 'Дата/время'),
    ('Когда запускать рекламу на ТВ', 'Дата/время')
) as attrs(attr_name, type_name)
ON CONFLICT (name) DO UPDATE SET 
    type_id = EXCLUDED.type_id;

-- Вставка фильмов (остаётся без изменений)
INSERT INTO entites (title) VALUES 
    ('Последний рассвет'),
    ('Тайна ледяного замка'),
    ('Космическая одиссея 2026'),
    ('Рождение героя'),
    ('Закат империи')
RETURNING entity_id, title;

-- Вставка значений атрибутов с динамическими датами
-- Фильм 1: Последний рассвет (все атрибуты)
INSERT INTO values (entity_id, attribute_id, value_string, value_bool, value_datetime) VALUES
    (1, 1, 'Потрясающий фильм с глубоким смыслом. Режиссёрская работа на высшем уровне.', NULL, NULL),
    (1, 2, NULL, true, NULL),
    (1, 3, NULL, false, NULL),
    (1, 4, NULL, NULL, CURRENT_DATE), -- сегодня+20 дней в 19:00
    (1, 5, NULL, NULL, CURRENT_DATE + INTERVAL '20 days' + INTERVAL '18 hours'), -- сегодня+25 дней в 18:00
    (1, 6, NULL, NULL, CURRENT_DATE+ INTERVAL '10 hours'),  -- сегодня+5 дней в 10:00
    (1, 7, NULL, NULL, CURRENT_DATE + INTERVAL '20 days' + INTERVAL '9 hours');   -- сегодня в 9:00

-- Фильм 2: Тайна ледяного замка (часть атрибутов)
INSERT INTO values (entity_id, attribute_id, value_string, value_bool, value_datetime) VALUES
    (2, 1, 'Захватывающая история с неожиданной развязкой. Визуальные эффекты превосходные.', NULL, NULL),
    (2, 3, NULL, true, NULL),
    (2, 4, NULL, NULL, CURRENT_DATE + INTERVAL '15 days' + INTERVAL '20 hours'), -- сегодня+15 дней в 20:00
    (2, 5, NULL, NULL, CURRENT_DATE + INTERVAL '40 days' + INTERVAL '17 hours'), -- сегодня+40 дней в 17:00
    (2, 6, NULL, NULL, CURRENT_DATE + INTERVAL '12 hours');  -- сегодня+3 дня в 12:00

-- Фильм 3: Космическая одиссея 2026 (часть атрибутов)
INSERT INTO values (entity_id, attribute_id, value_string, value_bool, value_datetime) VALUES
    (3, 1, 'Фильм переопределяет жанр научной фантастики. Музыка и операторская работа - шедевр.', NULL, NULL),
    (3, 2, NULL, true, NULL),
    (3, 4, NULL, NULL, CURRENT_DATE + INTERVAL '25 days' + INTERVAL '21 hours'), -- сегодня+25 дней в 21:00
    (3, 7, NULL, NULL, CURRENT_DATE + INTERVAL '20 days' + INTERVAL '8 hours');  -- сегодня+10 дней в 8:00

-- Фильм 4: Рождение героя (часть атрибутов)
INSERT INTO values (entity_id, attribute_id, value_string, value_datetime) VALUES
    (4, 1, 'Душевная история о становлении характера. Игра актёров заслуживает особой похвалы.', NULL),
    (4, 4, NULL, CURRENT_DATE + INTERVAL '35 days' + INTERVAL '18 hours 30 minutes'), -- сегодня+35 дней в 18:30
    (4, 5, NULL, CURRENT_DATE + INTERVAL '50 days' + INTERVAL '19 hours'),           -- сегодня+50 дней в 19:00
    (4, 6, NULL, CURRENT_DATE + INTERVAL '9 hours');            -- сегодня+20 дней в 9:00

-- Фильм 5: Закат империи (часть атрибутов)
INSERT INTO values (entity_id, attribute_id, value_string, value_bool, value_datetime) VALUES
    (5, 1, 'Эпическое полотно о исторических событиях. Костюмы и декорации поражают аутентичностью.', NULL, NULL),
    (5, 4, NULL, NULL, CURRENT_DATE + INTERVAL '45 days' + INTERVAL '20 hours'), -- сегодня+45 дней в 20:00
    (5, 6, NULL, NULL, CURRENT_DATE + INTERVAL '10 days' + INTERVAL '11 hours'); -- сегодня+30 дней в 11:00