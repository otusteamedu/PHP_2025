-- Оптимизации на объеме 10 000 000+ строк
-- Выполнять после загрузки данных (DML_10000000.sql).

-- 1) Индексы под реальные фильтры и join-предикаты
CREATE INDEX IF NOT EXISTS idx_screenings_start_time_hall_movie
    ON public.screenings (start_time, hall_id, movie_id);

-- Для Q2 (быстрый подсчет sold за период)
CREATE INDEX IF NOT EXISTS idx_tickets_sold_purchase_time
    ON public.tickets (purchase_time)
    WHERE status = 'sold';

-- Для Q4 (выручка по sold в разрезе screening)
CREATE INDEX IF NOT EXISTS idx_tickets_sold_screening_price
    ON public.tickets (screening_id, price)
    WHERE status = 'sold';

-- Для Q5 (занятые места: sold/reserved)
CREATE INDEX IF NOT EXISTS idx_tickets_screening_seat_occupied
    ON public.tickets (screening_id, seat_id)
    WHERE status IN ('sold', 'reserved');

-- Для Q6 (min/max цены по сеансу)
CREATE INDEX IF NOT EXISTS idx_tickets_screening_price
    ON public.tickets (screening_id, price);

-- 2) Обновляем статистики планировщика
ANALYZE public.screenings;
ANALYZE public.tickets;
ANALYZE public.seats;
ANALYZE public.movies;

-- 3) Рекомендуемые параметры на время массового анализа
-- SET work_mem = '128MB';
-- SET max_parallel_workers_per_gather = 4;
-- SET random_page_cost = 1.1;
