CREATE DATABASE IF NOT EXISTS cinema_eav;
USE cinema_eav;

CREATE TABLE IF NOT EXISTS films (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    code VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS attribute_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS attributes (
   id INT PRIMARY KEY AUTO_INCREMENT,
   title VARCHAR(255) NOT NULL,
   code VARCHAR(255) UNIQUE NOT NULL,
   is_service_attribute BOOL,
   attribute_type_id INT,
   FOREIGN KEY (attribute_type_id) REFERENCES attribute_types (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS film_attribute_values (
   id INT PRIMARY KEY AUTO_INCREMENT,
   film_id INT,
   attribute_id INT,
   value_text TEXT,
   value_varchar VARCHAR(255),
   value_int INT,
   value_float FLOAT,
   value_boolean BOOL,
   value_datetime DATETIME,
   FOREIGN KEY (film_id) REFERENCES films (id) ON DELETE CASCADE,
   FOREIGN KEY (attribute_id) REFERENCES attributes (id) ON DELETE CASCADE
);

CREATE INDEX idx_value_boolean ON film_attribute_values (value_boolean);
CREATE INDEX idx_full_info ON film_attribute_values (value_int,value_float,value_datetime);

INSERT INTO films (title, code) VALUES
('Терминатор', 'terminator'),
('Захват 2', 'capture_2'),
('Тупой и еще тупее', 'dumb_and_dumber'),
('Любовь и голуби', 'love_and_doves');

INSERT INTO attribute_types (code) VALUES ('text'),('varchar'),('int'),('float'),('boolean'),('datetime');

INSERT INTO attributes (title, code, is_service_attribute, attribute_type_id) VALUES
('Рецензии', 'reviews',0, (SELECT attribute_types.id FROM attribute_types WHERE attribute_types.code = 'text')),
('Мировая премьера', 'world_premiere',0, (SELECT attribute_types.id FROM attribute_types WHERE attribute_types.code = 'datetime')),
('Премьера в РФ', 'russian_premiere',0, (SELECT attribute_types.id FROM attribute_types WHERE attribute_types.code = 'datetime')),
('Дата начала рекламы на ТВ', 'adv_start_date_on_tv',1, (SELECT attribute_types.id FROM attribute_types WHERE attribute_types.code = 'datetime')),
('Дата начала продажи билетов', 'saling_start_date',1, (SELECT attribute_types.id FROM attribute_types WHERE attribute_types.code = 'datetime')),
('Премия (изображение)', 'prize_url',0, (SELECT attribute_types.id FROM attribute_types WHERE attribute_types.code = 'varchar')),
('Только 18+', '18_plus_only',0, (SELECT attribute_types.id FROM attribute_types WHERE attribute_types.code = 'boolean'));