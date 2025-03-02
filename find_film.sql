SELECT f.id, f.title, sum(t.price) as total_sum
FROM ticket t
         JOIN public.session s on s.id = t.session_id
         JOIN public.film f on f.id = s.film_id
GROUP BY f.id
ORDER BY total_sum DESC
LIMIT 1
;
