-- 1) Таблица фильмов
CREATE TABLE films (
  id            BIGSERIAL PRIMARY KEY,
  title         TEXT NOT NULL,
  original_title TEXT,
  released_year INT,
  created_at    TIMESTAMP WITH TIME ZONE DEFAULT now(),
  updated_at    TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- 2) Типы атрибутов (attribute types)
CREATE TABLE attribute_types (
  id   SMALLSERIAL PRIMARY KEY,
  code TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  data_type TEXT NOT NULL CHECK (data_type IN ('text','boolean','date','numeric','float','json')),
);

-- 3) Атрибуты (конкретные атрибуты для фильмов)
CREATE TABLE attributes (
  id                BIGSERIAL PRIMARY KEY,
  attribute_type_id SMALLINT NOT NULL REFERENCES attribute_types(id) ON DELETE RESTRICT,
  code              TEXT NOT NULL,
  name              TEXT NOT NULL,
  description       TEXT,
  UNIQUE(attribute_type_id, code)
);

-- 4) Значения атрибутов
CREATE TABLE attribute_values (
  id           BIGSERIAL PRIMARY KEY,
  film_id      BIGINT NOT NULL REFERENCES films(id) ON DELETE CASCADE,
  attribute_id BIGINT NOT NULL REFERENCES attributes(id) ON DELETE CASCADE,
  attribute_value   TEXT,
  value_number INTEGER,
  value_float  DOUBLE PRECISION,
  value_bool   BOOLEAN,
  value_date   DATE,
  value_json   JSONB,
  created_at   TIMESTAMP WITH TIME ZONE DEFAULT now(),
  updated_at   TIMESTAMP WITH TIME ZONE DEFAULT now(),
  -- ensure exactly one typed column is set (or allow all NULL if you want "empty" attribute)
  CONSTRAINT one_value_only CHECK (
    (CASE WHEN value_text   IS NOT NULL THEN 1 ELSE 0 END) +
    (CASE WHEN value_number IS NOT NULL THEN 1 ELSE 0 END) +
    (CASE WHEN value_float  IS NOT NULL THEN 1 ELSE 0 END) +
    (CASE WHEN value_bool   IS NOT NULL THEN 1 ELSE 0 END) +
    (CASE WHEN value_date   IS NOT NULL THEN 1 ELSE 0 END) +
    (CASE WHEN value_json   IS NOT NULL THEN 1 ELSE 0 END)
      = 1
    )
);

-- Индексы для быстрого поиска
CREATE INDEX idx_attr_values_film_attr ON attribute_values(film_id, attribute_id);
CREATE INDEX idx_attr_values_attr ON attribute_values(attribute_id);
CREATE INDEX idx_attr_values_date ON attribute_values(value_date);
CREATE INDEX idx_attr_values_num ON attribute_values(value_number);
CREATE INDEX idx_attr_values_float ON attribute_values(value_float);
CREATE INDEX idx_attr_values_text ON attribute_values USING gin (value_text gin_trgm_ops);
CREATE INDEX idx_attribute_types_code ON attribute_types(code);
CREATE INDEX idx_attributes_code ON attributes(code);

-- 5) View для маркетинга
CREATE OR REPLACE VIEW marketing_attributes AS
SELECT
  f.id      AS film_id,
  f.title   AS film_title,
  atype.code AS attribute_type_code,
  atype.name AS attribute_type_name,
  a.code    AS attribute_code,
  a.name    AS attribute_name,
  COALESCE(
    value_text,
    CASE WHEN value_number IS NOT NULL THEN value_number::text END,
    CASE WHEN value_float  IS NOT NULL THEN trim(to_char(value_float, 'FM9999999990.######')) END,
    CASE WHEN value_bool   IS NOT NULL THEN (CASE WHEN value_bool THEN 'true' ELSE 'false' END) END,
    CASE WHEN value_date   IS NOT NULL THEN to_char(value_date,'YYYY-MM-DD') END,
    CASE WHEN value_json   IS NOT NULL THEN value_json::text END
  ) AS value_text
FROM attribute_values v
  JOIN films f ON f.id = v.film_id
  JOIN attributes a ON a.id = v.attribute_id
  JOIN attribute_types atype ON atype.id = a.attribute_type_id;

-- 6) View для служебных данных
CREATE OR REPLACE VIEW service_tasks_view AS
SELECT
  f.id AS film_id,
  f.title AS film_title,
  COALESCE(
    array_agg( a.name || ' @ ' || to_char(v.value_date,'YYYY-MM-DD') ) FILTER (WHERE v.value_date = CURRENT_DATE),
    ARRAY[]::text[]
  ) AS tasks_today,
  COALESCE(
    array_agg( a.name || ' @ ' || to_char(v.value_date,'YYYY-MM-DD') ) FILTER (WHERE v.value_date = (CURRENT_DATE + INTERVAL '20 days')::date),
    ARRAY[]::text[]
  ) AS tasks_in_20_days
FROM films f
  LEFT JOIN attribute_values v ON v.film_id = f.id
  LEFT JOIN attributes a ON a.id = v.attribute_id
  LEFT JOIN attribute_types atype ON atype.id = a.attribute_type_id
WHERE atype.code = 'service_date'
GROUP BY f.id, f.title;
