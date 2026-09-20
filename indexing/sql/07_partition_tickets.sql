-- ДЗ-9 «Индексирование данных»
-- Структурная оптимизация: секционирование tickets по sold_at (RANGE, месяц).
--
-- Запросы 2 и 4 фильтруют sold_at >= now() - 7 days. Без секций читается
-- вся куча/индекс на 8 млн строк. С RANGE-секциями планировщик отсекает
-- месяцы вне интервала (partition pruning) — тема вебинара
-- «Секционирование (партиционирование)».
--
-- Ограничения PostgreSQL 12:
--   * UNIQUE/PK на секционированной таблице обязан включать ключ секции.
--     Было UNIQUE(screening_id, seat_id) — стало
--     UNIQUE(screening_id, seat_id, sold_at): чуть слабее, для этой нагрузки
--     sold_at у пары (сеанс, место) один.
--   * Запросы 5 и 6 фильтруют только screening_id, без sold_at — pruning
--     не сработает, Append пройдёт по всем секциям. Это сознательный
--     компромисс: ускоряем тяжёлые 2 и 4, точечные 5 и 6 остаются
--     миллисекундными за счёт индекса по screening_id.
--
-- Диапазон данных скрипта 03_fill_10m.sql: sold_at ~ июнь–октябрь 2026.
-- Выполняется на уже заполненной БД (после 03 + 05). Долго: копирование
-- 8 млн строк + пересоздание индексов.

SET maintenance_work_mem = '256MB';
SET work_mem = '64MB';

DROP TABLE IF EXISTS tickets_parted CASCADE;

CREATE TABLE tickets_parted
(
    id             integer        NOT NULL,
    screening_id   integer        NOT NULL REFERENCES screenings (id),
    seat_id        integer        NOT NULL REFERENCES seats (id),
    customer_email varchar(150),
    sold_at        timestamp      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    final_price    numeric(10, 2) NOT NULL
) PARTITION BY RANGE (sold_at);

CREATE TABLE tickets_2026_06 PARTITION OF tickets_parted
    FOR VALUES FROM ('2026-06-01') TO ('2026-07-01');
CREATE TABLE tickets_2026_07 PARTITION OF tickets_parted
    FOR VALUES FROM ('2026-07-01') TO ('2026-08-01');
CREATE TABLE tickets_2026_08 PARTITION OF tickets_parted
    FOR VALUES FROM ('2026-08-01') TO ('2026-09-01');
CREATE TABLE tickets_2026_09 PARTITION OF tickets_parted
    FOR VALUES FROM ('2026-09-01') TO ('2026-10-01');
CREATE TABLE tickets_2026_10 PARTITION OF tickets_parted
    FOR VALUES FROM ('2026-10-01') TO ('2026-11-01');
CREATE TABLE tickets_default PARTITION OF tickets_parted DEFAULT;

\echo '=== копирование 8 млн билетов в секции ==='
INSERT INTO tickets_parted (id, screening_id, seat_id, customer_email, sold_at, final_price)
SELECT id, screening_id, seat_id, customer_email, sold_at, final_price
FROM tickets;

ALTER TABLE tickets_parted
    ADD PRIMARY KEY (id, sold_at);
ALTER TABLE tickets_parted
    ADD CONSTRAINT tickets_screening_id_seat_id_sold_at_key
        UNIQUE (screening_id, seat_id, sold_at);

DROP TABLE tickets;
ALTER TABLE tickets_parted RENAME TO tickets;

-- SERIAL-последовательность принадлежала старой таблице и ушла вместе с DROP.
CREATE SEQUENCE tickets_id_seq;
SELECT setval('tickets_id_seq', (SELECT max(id) FROM tickets));
ALTER TABLE tickets
    ALTER COLUMN id SET DEFAULT nextval('tickets_id_seq');
ALTER SEQUENCE tickets_id_seq OWNED BY tickets.id;

\echo '=== индексы на родителе (локальные индексы на каждой секции) ==='
CREATE INDEX idx_tickets_sold_at
    ON tickets (sold_at) INCLUDE (screening_id, final_price);
CREATE INDEX idx_tickets_screening_final_price
    ON tickets (screening_id) INCLUDE (final_price);

VACUUM ANALYZE tickets;

\echo '=== секции и число строк ==='
SELECT inhrelid::regclass AS partition, n.n_live_tup AS rows
FROM pg_inherits i
         JOIN pg_stat_user_tables n ON n.relid = i.inhrelid
WHERE i.inhparent = 'tickets'::regclass
ORDER BY 1;
