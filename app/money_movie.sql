-- Сумма продаж по всем проданным билетам для каждого фильма
SELECT 
    m.title AS film_title, -- Название фильма
    SUM(t.final_price) AS total_revenue -- Общая сумма продаж
FROM Movies m
JOIN Screenings s ON m.id = s.movie_id -- Связь фильмов с сеансами
JOIN Tickets t ON s.id = t.screening_id -- Связь сеансов с билетами
GROUP BY m.id, m.title -- Группировка по фильмам
ORDER BY total_revenue DESC -- Сортировка по убыванию продаж
LIMIT 1; -- Только самый прибыльный фильм