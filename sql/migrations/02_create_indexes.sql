CREATE INDEX idx_attributes_attribute_type_id
    ON attributes(attribute_type_id);

CREATE INDEX idx_attribute_values_movie_id
    ON attribute_values(movie_id);

CREATE INDEX idx_attribute_values_attribute_id
    ON attribute_values(attribute_id);

CREATE INDEX idx_attribute_values_value_date
    ON attribute_values(value_date)
    WHERE value_date IS NOT NULL;

CREATE INDEX idx_attribute_values_value_boolean
    ON attribute_values(value_boolean)
    WHERE value_boolean IS NOT NULL;

CREATE INDEX idx_attribute_values_value_numeric
    ON attribute_values(value_numeric)
    WHERE value_numeric IS NOT NULL;

CREATE INDEX idx_attribute_values_value_text_not_null
    ON attribute_values(attribute_id)
    WHERE value_text IS NOT NULL;
