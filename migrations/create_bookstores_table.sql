CREATE TABLE bookstores (
                            id SERIAL PRIMARY KEY,
                            name VARCHAR(255) NOT NULL,
                            city VARCHAR(100) NOT NULL,
                            address VARCHAR(500) NOT NULL,
                            phone VARCHAR(20),
                            email VARCHAR(100),
                            established_year INTEGER,
                            square_meters NUMERIC(10, 2),
                            has_cafe BOOLEAN DEFAULT FALSE,
                            rating NUMERIC(3, 2) DEFAULT 0.00,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Заполнение тестовыми данными
INSERT INTO bookstores (name, city, address, phone, email, established_year, square_meters, has_cafe, rating) VALUES
                                                                                                                  ('Книжный мир', 'Москва', 'ул. Тверская, 25', '+7 (495) 123-45-67', 'info@knignymir.ru', 1995, 350.50, TRUE, 4.75),
                                                                                                                  ('Читай-город', 'Санкт-Петербург', 'Невский пр., 45', '+7 (812) 234-56-78', 'spb@chitay-gorod.ru', 2000, 280.75, TRUE, 4.60),
                                                                                                                  ('Буквоед', 'Москва', 'ул. Арбат, 15', '+7 (495) 345-67-89', 'arbat@bookvoed.ru', 1998, 420.25, FALSE, 4.80),
                                                                                                                  ('Лабиринт', 'Новосибирск', 'ул. Ленина, 78', '+7 (383) 456-78-90', 'novosibirsk@labirint.ru', 2005, 320.00, TRUE, 4.50),
                                                                                                                  ('Книжная лавка', 'Екатеринбург', 'ул. Малышева, 56', '+7 (343) 567-89-01', 'ekb@kniglavka.ru', 2010, 180.50, FALSE, 4.30),
                                                                                                                  ('Дом книги', 'Казань', 'ул. Баумана, 34', '+7 (843) 678-90-12', 'kazan@domknigi.ru', 2002, 250.75, TRUE, 4.65),
                                                                                                                  ('Книги и кофе', 'Ростов-на-Дону', 'ул. Большая Садовая, 67', '+7 (863) 789-01-23', 'rostov@bookscoffee.ru', 2015, 150.25, TRUE, 4.85),
                                                                                                                  ('Пушкинская лавка', 'Москва', 'ул. Пушкинская, 12', '+7 (495) 890-12-34', 'pushkin@lavka.ru', 1985, 200.00, FALSE, 4.70);

-- Добавим еще 100+ записей
DO $$
BEGIN
    FOR i IN 1..100 LOOP
INSERT INTO bookstores (name, city, address, phone, email, established_year, square_meters, has_cafe, rating)
VALUES (
           'Книжный магазин ' || i,
           CASE (i % 10)
               WHEN 0 THEN 'Москва'
               WHEN 1 THEN 'Санкт-Петербург'
               WHEN 2 THEN 'Новосибирск'
               WHEN 3 THEN 'Екатеринбург'
               WHEN 4 THEN 'Казань'
               WHEN 5 THEN 'Ростов-на-Дону'
               WHEN 6 THEN 'Нижний Новгород'
               WHEN 7 THEN 'Краснодар'
               WHEN 8 THEN 'Воронеж'
               ELSE 'Самара'
               END,
           'ул. Примерная, ' || (i * 3),
           '+7 (' || (900 + i) || ') ' || (1000000 + i),
           'store' || i || '@example.com',
           1990 + (i % 25),
           100 + (i * 2.5),
           i % 3 = 0,
           3.5 + (i % 15) * 0.1
       );
END LOOP;
END $$;