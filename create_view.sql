-- View: задачи по фильмам
CREATE OR REPLACE VIEW v_service_tasks AS
SELECT
	m.id AS movie_id,
	m.title AS movie_title,
	STRING_AGG(
		a.name || ' (' || TO_CHAR(av.value_date, 'YYYY-MM-DD') || ')',
		'; ' ORDER BY av.value_date
	) FILTER (WHERE av.value_date = CURRENT_DATE) AS tasks_actual_today,
	STRING_AGG(
		a.name || ' (' || TO_CHAR(av.value_date, 'YYYY-MM-DD') || ')',
		'; ' ORDER BY av.value_date
	) FILTER (WHERE av.value_date >= CURRENT_DATE + INTERVAL '20 days') AS tasks_in_20_days
FROM movie m
LEFT JOIN attribute_values av ON av.movie_id = m.id
LEFT JOIN attribute a ON a.id = av.attribute_id
LEFT JOIN attribute_type at ON at.id = a.attr_type_id
WHERE at.name IN ('Служебные даты', 'Важные даты')
  AND at.data_type = 'date'
  AND av.value_date IS NOT NULL
  AND (av.value_date = CURRENT_DATE OR av.value_date >= CURRENT_DATE + INTERVAL '20 days')
GROUP BY m.id, m.title;

-- View: данные для маркетинга
CREATE OR REPLACE VIEW v_marketing_data AS
SELECT
	m.id AS movie_id,
	m.title AS movie_title,
	at.name AS attribute_type,
	a.name AS attribute_name,
	CASE at.data_type
		WHEN 'string' THEN av.value_string
		WHEN 'boolean' THEN CASE WHEN av.value_boolean IS TRUE THEN 'Да' WHEN av.value_boolean IS FALSE THEN 'Нет' ELSE NULL END
		WHEN 'date' THEN TO_CHAR(av.value_date, 'YYYY-MM-DD')
		WHEN 'int' THEN av.value_int::TEXT
		WHEN 'numeric' THEN av.value_numeric::TEXT
		WHEN 'float' THEN av.value_float::TEXT
		WHEN 'json' THEN av.value_json::TEXT
		ELSE NULL
	END AS value_text
FROM movie m
INNER JOIN attribute_values av ON av.movie_id = m.id
INNER JOIN attribute a ON a.id = av.attribute_id
INNER JOIN attribute_type at ON at.id = a.attr_type_id;
