-- запрос на самый прибыльный фильм
SELECT f.name AS film_name,
       SUM(s.price) AS total_income
FROM ticket t, seance s, film f
WHERE t.seance_id = s.id
  AND s.film_id = f.id
  AND t.status = 'sold'
GROUP BY f.name
ORDER BY total_income DESC
    LIMIT 1;
