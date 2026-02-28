--перезаполняем только две таблицы, остальные данные остаются те же
TRUNCATE sessions;
TRUNCATE tickets;

-- заполнение сеансов (800,000 сеансов)
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
    generate_series(1, 8000)
ORDER BY random()
LIMIT 800000;

-- заполнение билетов (10,000,000 билетов)
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
LIMIT 10000000;