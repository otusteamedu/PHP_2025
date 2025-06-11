-- Создание таблицы категорий
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Вставка 10 категорий
INSERT INTO categories (name) VALUES
  ('Электроника'),
  ('Одежда'),
  ('Продукты питания'),
  ('Канцелярия'),
  ('Мебель'),
  ('Игрушки'),
  ('Косметика'),
  ('Автотовары'),
  ('Спорттовары'),
  ('Бытовая техника');

-- Создание таблицы продуктов
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price NUMERIC(10, 2) NOT NULL CHECK (price >= 0),
    category_id INTEGER REFERENCES categories(id)
);

-- Вставка первых 5 продуктов вручную
INSERT INTO products (name, price, category_id) VALUES
  ('Продукт 1', 1345.50, 1),
  ('Продукт 2', 2999.99, 3),
  ('Продукт 3', 899.00, 5),
  ('Продукт 4', 7450.00, 2),
  ('Продукт 5', 123.45, 4);

-- Генерация оставшихся 95 продуктов
DO $$
BEGIN
  FOR i IN 6..100 LOOP
    INSERT INTO products (name, price, category_id)
    VALUES (
      'Продукт ' || i,
      round((random() * 9900 + 100)::numeric, 2), 
      (SELECT id FROM categories ORDER BY random() LIMIT 1)
    );
  END LOOP;
END
$$;
