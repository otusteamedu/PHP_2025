-- поиск всех атрибутов для одного конкретного фильма
CREATE INDEX idx_attribute_values_film_id ON attribute_values (film_id);

-- поиск всех фильмов, у которых есть определенный атрибут
CREATE INDEX idx_attribute_values_attribute_id ON attribute_values (attribute_id);

-- поиск значения конкретного атрибута у конкретного фильма
CREATE INDEX idx_attribute_values_film_attribute ON attribute_values (film_id, attribute_id);

-- поиск всех записей с определённым значением даты
CREATE INDEX idx_attribute_values_date_value ON attribute_values (date_value);

-- поиск всех записей с определённым целочисленным значением
CREATE INDEX idx_attribute_values_integer_value ON attribute_values (integer_value);

-- поиск всех записей с определённым дробным значением
CREATE INDEX idx_attribute_values_decimal_value ON attribute_values (decimal_value);