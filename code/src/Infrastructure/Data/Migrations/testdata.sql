-- Заполнение фильмов 
INSERT INTO movies (title, duration_minutes, age_rating, start_distribution, end_distribution)
WITH movie_titles AS (
    SELECT * FROM (VALUES
        ('Арктический патруль'),
        ('Затерянный город'),
        ('Молчание океана'),
        ('Сияние звёзд'),
        ('Путь самурая'),
        ('Секреты Вселенной'),
        ('Последний викинг'),
        ('Эхо прошлого'),
        ('Горизонт событий'),
        ('Кристалл времени'),
        ('Огненные горы'),
        ('Тайный кодекс'),
        ('Полярная ночь'),
        ('Восход Венеры'),
        ('Заколдованный лес'),
        ('Зимний рассвет'),
        ('Ледяная легенда'),
        ('Снежные вершины'),
        ('Весеннее пробуждение'),
        ('Тайна старого парка'),
        ('Последний рубеж'),
        ('Летнее солнцестояние'),
        ('Океанские глубины'),
        ('Песок времени'),
        ('Знойное лето'),
        ('Тени мегаполиса'),
        ('Осенний листопад'),
        ('Хэллоуинские истории'),
        ('Новогоднее чудо'),
        ('Зимняя сказка'),
        ('Рассвет нового года')
        ) AS t(title)
),
random_dates AS (
    SELECT 
        title,
        (90 + random() * 70)::int AS duration,  -- 90-160 минут
        (ARRAY[0, 6, 12, 16, 18])[(random() * 4)::int + 1] AS age_rating,
        -- Случайная дата начала в 2025 году
        DATE '2025-01-01' + (random() * 364)::int AS start_date,
        -- Случайная длительность проката 45-180 дней
        (45 + random() * 135)::int AS distribution_days
    FROM movie_titles
)
SELECT 
    title,
    duration,
    age_rating,
    start_date,
    start_date + distribution_days AS end_date
FROM random_dates
WHERE start_date + distribution_days <= '2026-01-31'
ORDER BY start_date
RETURNING 
    movies_id,
    title,
    duration_minutes,
    age_rating,
    TO_CHAR(start_distribution, 'DD.MM.YYYY') AS start_date,
    TO_CHAR(end_distribution, 'DD.MM.YYYY') AS end_date,
    (end_distribution - start_distribution) || ' дней' AS distribution_period;

-- Заполнение залов
INSERT INTO halls (name) VALUES
('Красный зал'),
('Синий зал'),
('Зелёный зал'),
('Золотой зал'),
('Лиловый зал'),
('Оранжевый зал');

-- Заполнение зон
INSERT INTO zones (name) VALUES
('Стандарт'),
('Премиум'),
('VIP');



-- Заполнение мест (3 ряда по 5 мест в каждом зале)
-- Красный зал (id=1)
INSERT INTO seats (row, seat, halls_id, zones_id) VALUES
(1, 1, 1, 1), (1, 2, 1, 1), (1, 3, 1, 1), (1, 4, 1, 1), (1, 5, 1, 1),
(2, 1, 1, 2), (2, 2, 1, 2), (2, 3, 1, 2), (2, 4, 1, 2), (2, 5, 1, 2),
(3, 1, 1, 3), (3, 2, 1, 3), (3, 3, 1, 3), (3, 4, 1, 3), (3, 5, 1, 3);

-- Синий зал (id=2)
INSERT INTO seats (row, seat, halls_id, zones_id) VALUES
(1, 1, 2, 1), (1, 2, 2, 1), (1, 3, 2, 1), (1, 4, 2, 1), (1, 5, 2, 1),
(2, 1, 2, 2), (2, 2, 2, 2), (2, 3, 2, 2), (2, 4, 2, 2), (2, 5, 2, 2),
(3, 1, 2, 3), (3, 2, 2, 3), (3, 3, 2, 3), (3, 4, 2, 3), (3, 5, 2, 3);

-- Зеленый зал (id=3)
INSERT INTO seats (row, seat, halls_id, zones_id) VALUES
(1, 1, 3, 1), (1, 2, 3, 1), (1, 3, 3, 1), (1, 4, 3, 1), (1, 5, 3, 1),
(2, 1, 3, 2), (2, 2, 3, 2), (2, 3, 3, 2), (2, 4, 3, 2), (2, 5, 3, 2),
(3, 1, 3, 3), (3, 2, 3, 3), (3, 3, 3, 3), (3, 4, 3, 3), (3, 5, 3, 3);

INSERT INTO seats (row, seat, halls_id, zones_id) VALUES
(1, 1, 4, 1), (1, 2, 4, 1), (1, 3, 4, 1), (1, 4, 4, 1), (1, 5, 4, 1),
(2, 1, 4, 2), (2, 2, 4, 2), (2, 3, 4, 2), (2, 4, 4, 2), (2, 5, 4, 2),
(3, 1, 4, 3), (3, 2, 4, 3), (3, 3, 4, 3), (3, 4, 4, 3), (3, 5, 4, 3);

INSERT INTO seats (row, seat, halls_id, zones_id) VALUES
(1, 1, 5, 1), (1, 2, 5, 1), (1, 3, 5, 1), (1, 4, 5, 1), (1, 5, 5, 1),
(2, 1, 5, 2), (2, 2, 5, 2), (2, 3, 5, 2), (2, 4, 5, 2), (2, 5, 5, 2),
(3, 1, 5, 3), (3, 2, 5, 3), (3, 3, 5, 3), (3, 4, 5, 3), (3, 5, 5, 3);

INSERT INTO seats (row, seat, halls_id, zones_id) VALUES
(1, 1, 6, 1), (1, 2, 6, 1), (1, 3, 6, 1), (1, 4, 6, 1), (1, 5, 6, 1),
(2, 1, 6, 2), (2, 2, 6, 2), (2, 3, 6, 2), (2, 4, 6, 2), (2, 5, 6, 2),
(3, 1, 6, 3), (3, 2, 6, 3), (3, 3, 6, 3), (3, 4, 6, 3), (3, 5, 6, 3);



-- Заполнение цен для всех залов, зон, временных интервалов и дней
INSERT INTO prices (zones_id, halls_id, from_time, to_time, is_weekend, price) VALUES
-- Красный зал (id=1)
-- Стандарт
(1, 1, '10:00', '18:00', false, 300.00),
(1, 1, '18:00', '23:00', false, 350.00),
(1, 1, '23:00', '10:00', false, 250.00),
(1, 1, '10:00', '18:00', true, 400.00),
(1, 1, '18:00', '23:00', true, 450.00),
(1, 1, '23:00', '10:00', true, 300.00),
-- Премиум
(2, 1, '10:00', '18:00', false, 400.00),
(2, 1, '18:00', '23:00', false, 450.00),
(2, 1, '23:00', '10:00', false, 350.00),
(2, 1, '10:00', '18:00', true, 500.00),
(2, 1, '18:00', '23:00', true, 550.00),
(2, 1, '23:00', '10:00', true, 400.00),
-- VIP
(3, 1, '10:00', '18:00', false, 500.00),
(3, 1, '18:00', '23:00', false, 550.00),
(3, 1, '23:00', '10:00', false, 450.00),
(3, 1, '10:00', '18:00', true, 600.00),
(3, 1, '18:00', '23:00', true, 650.00),
(3, 1, '23:00', '10:00', true, 500.00),

-- Синий зал (id=2)
-- Стандарт
(1, 2, '10:00', '18:00', false, 280.00),
(1, 2, '18:00', '23:00', false, 330.00),
(1, 2, '23:00', '10:00', false, 230.00),
(1, 2, '10:00', '18:00', true, 380.00),
(1, 2, '18:00', '23:00', true, 430.00),
(1, 2, '23:00', '10:00', true, 280.00),
-- Премиум
(2, 2, '10:00', '18:00', false, 380.00),
(2, 2, '18:00', '23:00', false, 430.00),
(2, 2, '23:00', '10:00', false, 330.00),
(2, 2, '10:00', '18:00', true, 480.00),
(2, 2, '18:00', '23:00', true, 530.00),
(2, 2, '23:00', '10:00', true, 380.00),
-- VIP
(3, 2, '10:00', '18:00', false, 480.00),
(3, 2, '18:00', '23:00', false, 530.00),
(3, 2, '23:00', '10:00', false, 430.00),
(3, 2, '10:00', '18:00', true, 580.00),
(3, 2, '18:00', '23:00', true, 630.00),
(3, 2, '23:00', '10:00', true, 480.00),

-- Зеленый зал (id=3)
-- Стандарт
(1, 3, '10:00', '18:00', false, 320.00),
(1, 3, '18:00', '23:00', false, 370.00),
(1, 3, '23:00', '10:00', false, 270.00),
(1, 3, '10:00', '18:00', true, 420.00),
(1, 3, '18:00', '23:00', true, 470.00),
(1, 3, '23:00', '10:00', true, 320.00),
-- Премиум
(2, 3, '10:00', '18:00', false, 420.00),
(2, 3, '18:00', '23:00', false, 470.00),
(2, 3, '23:00', '10:00', false, 370.00),
(2, 3, '10:00', '18:00', true, 520.00),
(2, 3, '18:00', '23:00', true, 570.00),
(2, 3, '23:00', '10:00', true, 420.00),
-- VIP
(3, 3, '10:00', '18:00', false, 520.00),
(3, 3, '18:00', '23:00', false, 570.00),
(3, 3, '23:00', '10:00', false, 470.00),
(3, 3, '10:00', '18:00', true, 620.00),
(3, 3, '18:00', '23:00', true, 670.00),
(3, 3, '23:00', '10:00', true, 520.00);

-- Заполнение сеансов (600 сеансов)
INSERT INTO sessions (start_time, end_time, movies_id, halls_id)
SELECT 
    -- Время начала: ближайшие 60 дней, с 8:00 до 23:00
    DATE '2025-01-01' + (random() * 364)::int + 
    INTERVAL '8 hours' + 
    (random() * 15 * 60)::int * INTERVAL '1 minute' AS start_time,
    -- Время окончания: начало + длительность фильма + 20 минут
    DATE '2025-01-01' + (random() * 364)::int + 
    INTERVAL '8 hours' + 
    (random() * 15 * 60)::int * INTERVAL '1 minute' + 
    (m.duration_minutes + 20) * INTERVAL '1 minute' AS end_time,
    m.movies_id,
    h.halls_id
FROM 
    (SELECT movies_id, duration_minutes FROM movies) m
CROSS JOIN 
    (SELECT halls_id FROM halls ORDER BY random()) h
CROSS JOIN 
    generate_series(1, 20)  -- 20 сеансов на комбинацию
ORDER BY random()
LIMIT 600;

-- Заполнение билетов (10,000 билетов)
INSERT INTO tickets (seats_id, sessions_id, status, final_price, update_status_dt)
SELECT 
    s.seats_id,
    sess.sessions_id,
    -- Распределение статусов: 20% свободно, 70% продано, 10% забронировано
    CASE floor(random() * 10)
        WHEN 0 THEN 'booked'
        WHEN 8 THEN 'free'
        WHEN 9 THEN 'free'
        ELSE 'sold'
    END AS status,
    -- Цена: базовая цена зоны * коэффициенты
    CASE 
        WHEN z.zones_id = 1 THEN 400.00
        WHEN z.zones_id = 2 THEN 300.00
        WHEN z.zones_id = 3 THEN 250.00
        ELSE 200.00
    END *
    CASE WHEN EXTRACT(HOUR FROM sess.start_time) >= 18 THEN 1.2 ELSE 1.0 END *
    CASE WHEN EXTRACT(DOW FROM sess.start_time) IN (0, 6) THEN 1.25 ELSE 1.0 END *
    CASE floor(random() * 4)  -- Случайная скидка
        WHEN 0 THEN 0.9  -- 10% скидка
        WHEN 1 THEN 0.95  -- 5% скидка
        ELSE 1.0
    END AS final_price,
    -- Время обновления: от 1 дня до 2 часов до сеанса
    sess.start_time - INTERVAL '1 day' + 
    (random() * 22 * 60 * 60)::int * INTERVAL '1 second' AS update_status_dt
FROM 
    (SELECT * FROM sessions ORDER BY random()) sess
JOIN 
    seats s ON sess.halls_id = s.halls_id
JOIN 
    zones z ON s.zones_id = z.zones_id
ORDER BY random()
LIMIT 10000;