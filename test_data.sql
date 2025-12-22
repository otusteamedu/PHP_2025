-- Очистка данных для повторного запуска
TRUNCATE TABLE attribute_values, attribute, attribute_type, movie RESTART IDENTITY CASCADE;

-- Фильмы
INSERT INTO movie (title, duration, description) VALUES
('Дюна: Часть вторая', 166, 'Продолжение истории Пола Атрейдеса и битвы за Арракис.'),
('Оппенгеймер', 180, 'Биографическая драма о создателе атомной бомбы.'),
('Бедные-несчастные', 141, 'История Беллы Бакстер в викторианской фантазии.');

-- Типы атрибутов
INSERT INTO attribute_type (name, data_type) VALUES
('Рецензии', 'string'),
('Премии', 'boolean'),
('Важные даты', 'date'),
('Служебные даты', 'date');

-- Атрибуты
INSERT INTO attribute (attr_type_id, name, description) VALUES
((SELECT id FROM attribute_type WHERE name = 'Рецензии'), 'Рецензия критика', 'Основная рецензия профильного критика'),
((SELECT id FROM attribute_type WHERE name = 'Рецензии'), 'Отзыв киноакадемии', 'Комментарий неизвестной киноакадемии'),
((SELECT id FROM attribute_type WHERE name = 'Рецензии'), 'Рецензия прессы', 'Краткий отзыв из прессы'),

((SELECT id FROM attribute_type WHERE name = 'Премии'), 'Оскар', 'Наличие премии Оскар'),
((SELECT id FROM attribute_type WHERE name = 'Премии'), 'Ника', 'Наличие премии Ника'),

((SELECT id FROM attribute_type WHERE name = 'Важные даты'), 'Мировая премьера', 'Дата мировой премьеры фильма'),
((SELECT id FROM attribute_type WHERE name = 'Важные даты'), 'Премьера в РФ', 'Дата премьеры в России'),

((SELECT id FROM attribute_type WHERE name = 'Служебные даты'), 'Старт продаж билетов', 'Когда открываются продажи билетов'),
((SELECT id FROM attribute_type WHERE name = 'Служебные даты'), 'Запуск рекламы на ТВ', 'Дата старта рекламной кампании на ТВ');

-- Рецензии
INSERT INTO attribute_values (movie_id, attribute_id, value_string) VALUES
((SELECT id FROM movie WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM attribute WHERE name = 'Рецензия критика'), 'Визуально мощный и вдумчивый эпос, усиливающий первую часть.'),
((SELECT id FROM movie WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM attribute WHERE name = 'Отзыв киноакадемии'), 'Амбициозное продолжение с акцентом на характеры.'),
((SELECT id FROM movie WHERE title = 'Оппенгеймер'), (SELECT id FROM attribute WHERE name = 'Рецензия критика'), 'Напряжённый портрет учёного, балансирующий на грани морали.'),
((SELECT id FROM movie WHERE title = 'Оппенгеймер'), (SELECT id FROM attribute WHERE name = 'Рецензия прессы'), 'Сильная актёрская игра и звуковой дизайн.'),
((SELECT id FROM movie WHERE title = 'Бедные-несчастные'), (SELECT id FROM attribute WHERE name = 'Рецензия критика'), 'Стильная и дерзкая викторианская сказка.'),
((SELECT id FROM movie WHERE title = 'Бедные-несчастные'), (SELECT id FROM attribute WHERE name = 'Отзыв киноакадемии'), 'Неожиданный микс абсурда и драмы.');

-- Премии
INSERT INTO attribute_values (movie_id, attribute_id, value_boolean) VALUES
((SELECT id FROM movie WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM attribute WHERE name = 'Оскар'), FALSE),
((SELECT id FROM movie WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM attribute WHERE name = 'Ника'), FALSE),
((SELECT id FROM movie WHERE title = 'Оппенгеймер'), (SELECT id FROM attribute WHERE name = 'Оскар'), TRUE),
((SELECT id FROM movie WHERE title = 'Оппенгеймер'), (SELECT id FROM attribute WHERE name = 'Ника'), FALSE),
((SELECT id FROM movie WHERE title = 'Бедные-несчастные'), (SELECT id FROM attribute WHERE name = 'Оскар'), TRUE),
((SELECT id FROM movie WHERE title = 'Бедные-несчастные'), (SELECT id FROM attribute WHERE name = 'Ника'), FALSE);

-- Важные даты
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES
((SELECT id FROM movie WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM attribute WHERE name = 'Мировая премьера'), CURRENT_DATE),
((SELECT id FROM movie WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM attribute WHERE name = 'Премьера в РФ'), CURRENT_DATE),
((SELECT id FROM movie WHERE title = 'Оппенгеймер'), (SELECT id FROM attribute WHERE name = 'Мировая премьера'), '2025-07-21'),
((SELECT id FROM movie WHERE title = 'Оппенгеймер'), (SELECT id FROM attribute WHERE name = 'Премьера в РФ'), '2025-08-10'),
((SELECT id FROM movie WHERE title = 'Бедные-несчастные'), (SELECT id FROM attribute WHERE name = 'Мировая премьера'), '2025-09-01'),
((SELECT id FROM movie WHERE title = 'Бедные-несчастные'), (SELECT id FROM attribute WHERE name = 'Премьера в РФ'), '2025-01-25');

-- Служебные даты
INSERT INTO attribute_values (movie_id, attribute_id, value_date) VALUES
((SELECT id FROM movie WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM attribute WHERE name = 'Старт продаж билетов'), '2026-02-15'),
((SELECT id FROM movie WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM attribute WHERE name = 'Запуск рекламы на ТВ'), '2026-02-01'),
((SELECT id FROM movie WHERE title = 'Оппенгеймер'), (SELECT id FROM attribute WHERE name = 'Старт продаж билетов'), '2025-07-01'),
((SELECT id FROM movie WHERE title = 'Оппенгеймер'), (SELECT id FROM attribute WHERE name = 'Запуск рекламы на ТВ'), '2025-06-20'),
((SELECT id FROM movie WHERE title = 'Бедные-несчастные'), (SELECT id FROM attribute WHERE name = 'Старт продаж билетов'), '2025-08-20'),
((SELECT id FROM movie WHERE title = 'Бедные-несчастные'), (SELECT id FROM attribute WHERE name = 'Запуск рекламы на ТВ'), '2025-08-01');

