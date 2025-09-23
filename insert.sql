
insert into movie(name, description) values 
('Начало', 'Кобб – талантливый вор, лучший из лучших в опасном искусстве извлечения: он крадет ценные секреты из глубин подсознания во время сна, когда человеческий разум наиболее уязвим. Редкие способности Кобба сделали его ценным игроком в привычном к предательству мире промышленного шпионажа, но они же превратили его в извечного беглеца и лишили всего, что он когда-либо любил.'),
('Интерстеллар', 'Когда засуха, пыльные бури и вымирание растений приводят человечество к продовольственному кризису, коллектив исследователей и учёных отправляется сквозь червоточину (которая предположительно соединяет области пространства-времени через большое расстояние) в путешествие, чтобы превзойти прежние ограничения для космических путешествий человека и найти планету с подходящими для человечества условиями.'),
('Побег из Шоушенка', 'Бухгалтер Энди Дюфрейн обвинён в убийстве собственной жены и её любовника. Оказавшись в тюрьме под названием Шоушенк, он сталкивается с жестокостью и беззаконием, царящими по обе стороны решётки. Каждый, кто попадает в эти стены, становится их рабом до конца жизни. Но Энди, обладающий живым умом и доброй душой, находит подход как к заключённым, так и к охранникам, добиваясь их особого к себе расположения.'),
('Джентльмены', 'Гангстеры всех мастей делят наркоферму. Закрученная экшен-комедия Гая Ричи с Мэттью Макконахи и Хью Грантом');

insert into attribute_type(name)
values  ('Рецензии'),
        ('Премия'),
        ('Дата'),
        ('Служебная дата'),
        ('Бюджет'),
        ('Оценка'),
        ('Место в рейтинге'),
        ('Сборы');


insert into attribute(attribute_type_id, name)
values  (1, 'Отзыв критика'),
        (1, 'Пользовательский отзыв'),
        (1, 'Отзыв киноакадемии'),
        (2, 'Оскар'),
        (2, 'Ника'),
        (2, 'Золотой глобус'),
        (3, 'Премьера в мире'),
        (3, 'Премьера в России'),
        (3, 'Выход на носителях'),
        (3, 'Релиз в онлайн кинотеатрах'),
        (4, 'Начало продажи билетов'),
        (4, 'Запуск рекламы на ТВ'),
        (4, 'Первый показ'),
        (5, 'Бюджет в $ на момент релиза'),
        (5, 'Бюджет в руб. на момент релиза'),
        (6, 'Рейтинг IMDB'),
        (6, 'Рейтинг Кинопоиск'),
        (6, 'Рейтинг MovieDB'),
        (7, 'Топ 250 Кинопоиск'),
        (7, 'Top IMDB'),
        (8, 'Сборы в мире'),
        (8, 'Сборы в России');


insert into value(entity_id, attribute_id, string_value, int_value, float_value, date_value, bool_value) values
-- Отзывы
(1, 1, 'Шедевр Нолана', NULL, NULL, NULL, NULL),
(1, 2, 'Отличный фильм для просмотра', NULL, NULL, NULL, NULL),
(1, 3, 'Лучший сценарий года', NULL, NULL, NULL, NULL),
-- Премии
(1, 4, NULL, NULL, NULL, NULL, TRUE),
(1, 5, NULL, NULL, NULL, NULL, FALSE),
(1, 6, NULL, NULL, NULL, NULL, TRUE), 
-- Даты
(1, 7, NULL, NULL, NULL, '2010-07-16', NULL), 
(1, 8, NULL, NULL, NULL, '2010-08-26', NULL), 
-- Служебные даты
(1, 9, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '40 days', NULL), 
(1, 10, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '25 days', NULL),
(1, 11, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '10 days', NULL),
(1, 12, NULL, NULL, NULL, CURRENT_DATE, NULL), 
(1, 13, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '20 days', NULL), 

-- Бюджет
(1, 14, NULL, 16000000000, NULL, NULL, NULL),
-- Оценки
(1, 16, NULL, NULL, 8.8, NULL, NULL),
(1, 17, NULL, NULL, 8.7, NULL, NULL),
-- Место в рейтинге
(1, 19, NULL, 10, NULL, NULL, NULL),
(1, 20, NULL, 8, NULL, NULL, NULL),
-- Сборы
(1, 21, NULL, 83680000000, NULL, NULL, NULL);

--  'Интерстеллар'
insert into value(entity_id, attribute_id, string_value, int_value, float_value, date_value, bool_value) values
-- Отзывы
(2, 1, 'Визуальный шедевр', NULL, NULL, NULL, NULL),
(2, 2, 'Сложный, но интересный фильм', NULL, NULL, NULL, NULL),
-- Премии
(2, 4, NULL, NULL, NULL, NULL, TRUE),
(2, 6, NULL, NULL, NULL, NULL, TRUE),

--Служебные даты
(2, 9, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '80 days', NULL),
(2, 10, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '90 days', NULL),
(2, 11, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '19 days', NULL),
(2, 12, NULL, NULL, NULL, CURRENT_DATE, NULL),
(2, 13, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '25 days', NULL),

-- Даты
(2, 7, NULL, NULL, NULL, '2014-10-26', NULL),
(2, 8, NULL, NULL, NULL, '2014-11-06', NULL),
-- Бюджет
(2, 14, NULL, 16500000000, NULL, NULL, NULL),
-- Оценки
(2, 16, NULL, NULL, 8.6, NULL, NULL),
(2, 17, NULL, NULL, 8.6, NULL, NULL),
-- Место в рейтинге
(2, 19, NULL, 15, NULL, NULL, NULL),
-- Сборы
(2, 21, NULL, 67750000000, NULL, NULL, NULL);

-- 'Побег из Шоушенка'
insert into value(entity_id, attribute_id, string_value, int_value, float_value, date_value, bool_value) values
-- Отзывы
(3, 1, 'Лучший фильм всех времен', NULL, NULL, NULL, NULL),
(3, 2, 'Трогательная история о надежде', NULL, NULL, NULL, NULL),
-- Премии
(3, 4, NULL, NULL, NULL, NULL, FALSE),
(3, 5, NULL, NULL, NULL, NULL, TRUE),

(3, 9, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '15 days', NULL),
(3, 10, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '10 days', NULL),
(3, 11, NULL, NULL, NULL, CURRENT_DATE - INTERVAL '40 days', NULL),
(3, 12, NULL, NULL, NULL, CURRENT_DATE - INTERVAL '60 days', NULL),
(3, 13, NULL, NULL, NULL, CURRENT_DATE, NULL),

-- Даты
(3, 7, NULL, NULL, NULL, '1994-09-23', NULL),
(3, 8, NULL, NULL, NULL, '1995-04-06', NULL),
-- Оценки
(3, 16, NULL, NULL, 9.3, NULL, NULL),
(3, 17, NULL, NULL, 9.1, NULL, NULL),
-- Место в рейтинге
(3, 19, NULL, 1, NULL, NULL, NULL),
(3, 20, NULL, 1, NULL, NULL, NULL),
-- Сборы
(3, 21, NULL, 1600000000, NULL, NULL, NULL);

-- 'Джентльмены' 
insert into value(entity_id, attribute_id, string_value, int_value, float_value, date_value, bool_value) values
-- Отзывы
(4, 1, 'Отличная экшен-комедия', NULL, NULL, NULL, NULL),
(4, 2, 'Гай Ричи не подвел', NULL, NULL, NULL, NULL),

--Служебные даты
(4, 9, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '10 days', NULL),
(4, 10, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '15 days', NULL),
(4, 11, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '20 days', NULL),
(4, 12, NULL, NULL, NULL, CURRENT_DATE, NULL),
(4, 13, NULL, NULL, NULL, CURRENT_DATE + INTERVAL '20 days', NULL),

-- Даты
(4, 7, NULL, NULL, NULL, '2019-12-03', NULL),
(4, 8, NULL, NULL, NULL, '2020-02-13', NULL),
-- Бюджет
(4, 14, NULL, 2200000000, NULL, NULL, NULL),
-- Оценки
(4, 16, NULL, NULL, 7.8, NULL, NULL),
(4, 17, NULL, NULL, 8.5, NULL, NULL),
-- Сборы
(4, 21, NULL, 11200000000, NULL, NULL, NULL);