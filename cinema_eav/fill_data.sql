INSERT INTO attribute_types(type_name) VALUES
    ('reviews'),
    ('award_flag'),
    ('important_date'),
    ('service_date'),
    ('score');

INSERT INTO attributes(attribute_name, type_id) VALUES
    ('critic_review',      1),
    ('academy_review',     1),
    ('oscar',              2),
    ('palm_of_cannes',     2),
    ('world_premiere',     3),
    ('tickets_sale_start', 4),
    ('tv_ad_start',        4),
    ('imdb_score',         5),
    ('award_count',        5);

INSERT INTO films(title, year) VALUES
    ('The Debuggers',      2021),
    ('Hackathon Heroes',   2022),
    ('Code Monkeys',       2023);

-- для The Debuggers
INSERT INTO attribute_values (
    film_id, attribute_id,
    text_value, date_value, boolean_value, integer_value, decimal_value
) VALUES
      (1, 1, 'Фильм, который заставит вас плакать над логами.', NULL, NULL, NULL, NULL),
      (1, 3, NULL, NULL, TRUE, NULL, NULL),
      (1, 5, NULL, '2021-09-01', NULL, NULL, NULL),
      (1, 6, NULL, CURRENT_DATE, NULL, NULL, NULL),
      (1, 7, NULL, CURRENT_DATE + INTERVAL '20 days', NULL, NULL, NULL),
      (1, 8, NULL, NULL, NULL, NULL, 9.1),
      (1, 9, NULL, NULL, NULL, 3, NULL);

-- для Hackathon Heroes
INSERT INTO attribute_values (
    film_id, attribute_id,
    text_value, date_value, boolean_value, integer_value, decimal_value
) VALUES
      (2, 2, 'Когда хакатонщики кодят, ООП прячется под диван.', NULL, NULL, NULL, NULL),
      (2, 4, NULL, NULL, FALSE, NULL, NULL),
      (2, 5, NULL, '2022-05-15', NULL, NULL, NULL),
      (2, 6, NULL, CURRENT_DATE + INTERVAL '5 days', NULL, NULL, NULL),
      (2, 7, NULL, CURRENT_DATE + INTERVAL '25 days', NULL, NULL, NULL),
      (2, 8, NULL, NULL, NULL, NULL, 8.3),
      (2, 9, NULL, NULL, NULL, 1, NULL);

-- для Code Monkeys
INSERT INTO attribute_values (
    film_id, attribute_id,
    text_value, date_value, boolean_value, integer_value, decimal_value
) VALUES
      (3, 1, 'Когда компилятор начинает играть против тебя.', NULL, NULL, NULL, NULL),
      (3, 3, NULL, NULL, FALSE, NULL, NULL),
      (3, 5, NULL, '2023-11-11', NULL, NULL, NULL),
      (3, 6, NULL, CURRENT_DATE, NULL, NULL, NULL),
      (3, 7, NULL, CURRENT_DATE + INTERVAL '20 days', NULL, NULL, NULL),
      (3, 8, NULL, NULL, NULL, NULL, 7.8),
      (3, 9, NULL, NULL, NULL, 0, NULL);
