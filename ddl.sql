-- Создание таблицы объектов тип перечисления
CREATE TYPE enum_order_status AS ENUM ('reserved', 'paid', 'cancelled');
CREATE TYPE enum_film_limitation AS ENUM ('0+', '6+', '12+', '16+', '18+');
CREATE TYPE enum_place_type AS ENUM ('Economy', 'Standart', 'Comfort', 'Love', 'Vip');
CREATE TYPE enum_hall_category AS ENUM ('Standart', 'Comfort', 'Dolby Atmos', 'IMAX', '3D', 'VIP');

-- Создание таблицы кинотеатров
CREATE TABLE cinema
(
    id      SERIAL PRIMARY KEY,
    name    VARCHAR(255) NOT NULL,
    city    VARCHAR(255) NOT NULL,
    address TEXT         NOT NULL
);

-- Создание таблицы клиентов
CREATE TABLE clients
(
    id    SERIAL PRIMARY KEY,
    name  VARCHAR(255),
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(255)
);

-- Создание таблицы залов
CREATE TABLE halls
(
    id        SERIAL PRIMARY KEY,
    name      VARCHAR(255)       NOT NULL,
    category  enum_hall_category NOT NULL,
    cinema_id INT                NOT NULL REFERENCES cinema (id) ON DELETE CASCADE
);

-- Создание таблицы фильмов
CREATE TABLE films
(
    id               SERIAL PRIMARY KEY,
    is_active        BOOLEAN              NOT NULL DEFAULT true,
    sort             INT                  NOT NULL DEFAULT 100,
    title            VARCHAR(255)         NOT NULL,
    actors           TEXT,
    description      TEXT,
    release          INT,
    genre            VARCHAR(255),
    limitation       enum_film_limitation NOT NULL,
    show_period_from TIMESTAMP,
    show_period_to   TIMESTAMP
);

-- Создание таблицы заказов
CREATE TABLE orders
(
    id         SERIAL PRIMARY KEY,
    client_id  INT,
    created_at TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP         NOT NULL,
    paid_at    TIMESTAMP         DEFAULT NULL,
    status     enum_order_status NOT NULL DEFAULT 'reserved',
    CONSTRAINT "fk-orders-client_id" FOREIGN KEY (client_id) REFERENCES clients (id)
);

-- Создание таблицы сессий
CREATE TABLE sessions
(
    id         SERIAL PRIMARY KEY,
    film_id    INT       NOT NULL,
    hall_id    INT       NOT NULL,
    created_at TIMESTAMP NOT NULL,
    CONSTRAINT "fk-sessions-film_id" FOREIGN KEY (film_id) REFERENCES films (id),
    CONSTRAINT "fk-sessions-hall_id" FOREIGN KEY (hall_id) REFERENCES halls (id)
);

-- Создание таблицы категории мест
CREATE TABLE place_categories
(
    id   SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    type enum_place_type NOT NULL
);

-- Создание таблицы мест
CREATE TABLE places
(
    id                SERIAL PRIMARY KEY,
    place_category_id INT NOT NULL REFERENCES place_categories (id) ON DELETE CASCADE,
    hall_id           INT NOT NULL REFERENCES halls (id) ON DELETE CASCADE,
    row               INT NOT NULL CHECK (row > 0),
    number            INT NOT NULL CHECK (number > 0),
    CONSTRAINT "fk-places-place_category_id" FOREIGN KEY (place_category_id) REFERENCES place_categories (id),
    CONSTRAINT "fk-places-hall_id" FOREIGN KEY (hall_id) REFERENCES halls (id)
);

-- Создание таблицы прайс-лист
CREATE TABLE price_list
(
    id                  SERIAL PRIMARY KEY,
    session_id          INT            NOT NULL,
    place_category_id   INT            NOT NULL,
    price               DECIMAL(10, 2) NOT NULL CHECK (price > 0),
    CONSTRAINT "fk-price_list-session_id" FOREIGN KEY (session_id) REFERENCES sessions (id),
    CONSTRAINT "fk-price_list-place_category_id" FOREIGN KEY (place_category_id) REFERENCES place_categories (id)
);

-- Создание таблицы билетов
CREATE TABLE tickets
(
    id          SERIAL PRIMARY KEY,
    order_id    INT            NOT NULL,
    session_id  INT            NOT NULL,
    place_id    INT            NOT NULL,
    price       DECIMAL(10, 2) NOT NULL CHECK (price > 0),
    CONSTRAINT "fk-tickets-order_id" FOREIGN KEY (order_id) REFERENCES orders (id),
    CONSTRAINT "fk-tickets-session_id" FOREIGN KEY (session_id) REFERENCES sessions (id),
    CONSTRAINT "fk-tickets-place_id" FOREIGN KEY (place_id) REFERENCES places (id)
);
