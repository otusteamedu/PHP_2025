SELECT
    f.id AS film_id,
    f.title AS film_title,
    SUM(f.base_price * h.price_premium * s.price_premium * sc.price_premium) AS total_revenue
FROM
    films AS f
JOIN sessions AS s ON f.id = s.film_id
JOIN halls AS h ON s.hall_id = h.id
JOIN seats AS st ON st.hall_id = h.id
JOIN seats_class AS sc ON st.seat_class_id = sc.id
JOIN tickets AS t ON t.seat_id = st.id
GROUP BY f.id, f.title
ORDER BY total_revenue DESC
LIMIT 1;