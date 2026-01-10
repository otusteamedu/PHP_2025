-- Индекс для ускорения поиска по movie_id в таблице values
CREATE INDEX idx_values_movie_id ON values(movie_id);

-- Индекс для ускорения поиска по attribute_id в таблице values
CREATE INDEX idx_values_attribute_id ON values(attribute_id);

-- Индекс для ускорения поиска по attribute_type_id в таблице attributes
CREATE INDEX idx_attributes_attribute_type_id ON attributes(attribute_type_id);
