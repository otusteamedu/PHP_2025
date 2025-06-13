-- представление для маркетинга
CREATE VIEW marketing_view AS
SELECT 
    m.title AS movie,
    a.attribute_name AS attribute_name,
    COALESCE(v.value_text::TEXT, v.value_date::TEXT, v.value_boolean::TEXT, v.value_integer::TEXT, v.value_real::TEXT, v.value_decimal::TEXT) AS attribute_value,
    at.type_name AS attribute_type
FROM movies m
LEFT JOIN values v ON v.movie_id = m.id
LEFT JOIN attributes a ON a.id = v.attribute_id
LEFT JOIN attribute_types at ON at.id = a.attribute_type_id;

-- представление для служебных данных
CREATE VIEW service_view AS
WITH cte_data AS (
	SELECT m.id AS movie_id, a.attribute_name, at.type_name, v.value_date
	FROM movies m
	LEFT JOIN values v ON v.movie_id = m.id
	LEFT JOIN attributes a ON a.id = v.attribute_id
	LEFT JOIN attribute_types at ON at.id = a.attribute_type_id
),
cte_today AS (
	SELECT 
	    movie_id,
	    STRING_AGG(attribute_name, ', ') AS tasks_today
	FROM cte_data
	WHERE type_name = 'Service Start Date' AND value_date <= CURRENT_DATE
	GROUP BY movie_id
),
cte_in_20_days AS (
	SELECT 
	    movie_id,
	    STRING_AGG(attribute_name, ', ') AS tasks_in_20_days
	FROM cte_data
	WHERE type_name = 'Service Start Date' AND value_date <= (CURRENT_DATE + 20)
	GROUP BY movie_id
)
SELECT m.title, COALESCE(c.tasks_today, '-') AS tasks_today, COALESCE(c20.tasks_in_20_days, '-') AS tasks_in_20_days
FROM movies m
LEFT JOIN cte_today c ON c.movie_id = m.id
LEFT JOIN cte_in_20_days c20 ON c20.movie_id = m.id;

-- представление - рейтинг фильмов
CREATE VIEW rating_view AS
WITH cte_imdb AS (
	SELECT m.title, v.value_real
	FROM movies m
	LEFT JOIN values v ON v.movie_id = m.id
	LEFT JOIN attributes a ON a.id = v.attribute_id
	WHERE a.attribute_name = 'Imdb Rating'
), cte_kp AS (
	SELECT m.title, v.value_real
	FROM movies m
	LEFT JOIN values v ON v.movie_id = m.id
	LEFT JOIN attributes a ON a.id = v.attribute_id
	WHERE a.attribute_name = 'Kinopoisk Rating'
), cte_awards AS (
	SELECT m.title, COUNT(*) AS awards_count
	FROM movies m
	LEFT JOIN values v ON v.movie_id = m.id
	LEFT JOIN attributes a ON a.id = v.attribute_id
	LEFT JOIN attribute_types at ON at.id = a.attribute_type_id
	WHERE at.type_name = 'Award' AND v.value_boolean IS NOT NULL AND v.value_boolean
	GROUP BY m.title
)
SELECT ci.title, COALESCE(ca.awards_count, 0) AS awards_count, ci.value_real AS imdb_rating, ck.value_real AS kinopoisk_rating
FROM cte_imdb ci
LEFT JOIN cte_awards ca ON ca.title = ci.title
LEFT JOIN cte_kp ck ON ck.title = ci.title
ORDER BY 2 DESC, 3 DESC, 4 DESC;