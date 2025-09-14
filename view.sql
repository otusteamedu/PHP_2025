CREATE VIEW
    service_data AS
SELECT
    *
FROM
    (
        SELECT
            film.name AS film,
            CASE
                WHEN atr_value.date_value = CURRENT_DATE THEN atr_film.name
            END AS today_event,
            CASE
                WHEN atr_value.date_value >= CURRENT_DATE + INTERVAL '20 days' THEN atr_film.name
            END AS days20_event
        FROM
            atr_value
            JOIN film ON film.id = atr_value.id_film
            JOIN atr_film ON atr_film.id = atr_value.id_atr_film
            JOIN atr_type ON atr_type.id = atr_film.id_atr_type
        WHERE
            atr_type.type_name = 'служебные даты'
    )
WHERE
    today_event IS NOT NULL
    OR days20_event IS NOT NULL;



CREATE VIEW
    marketing_data AS
SELECT
    film.name AS film,
    atr_type.type_name AS atr_type,
    atr_film.name as atr,
    COALESCE(
        atr_value.int_value::TEXT,
        atr_value.text_value::TEXT,
        atr_value.decimal_value::TEXT,
        atr_value.date_value::TEXT,
        atr_value.boolean_value::TEXT
    ) as value
FROM
    atr_value
    JOIN film ON film.id = atr_value.id_film
    JOIN atr_film ON atr_film.id = atr_value.id_atr_film
    JOIN atr_type ON atr_type.id = atr_film.id_atr_type;