-- ДЗ-9 «Индексирование данных»
-- Настройки PostgreSQL на уровне администрирования сервера.
--
-- Индексы ускоряют доступ к строкам, но не меняют то, как сервер
-- кэширует страницы 8 КБ, оценивает стоимость I/O и держит Hash/Sort
-- в памяти. Это как раз тема занятия: shared_buffers, work_mem,
-- модель стоимости планировщика, WAL, JIT.
--
-- shared_buffers применяется только после рестарта.
-- Остальное подхватывает SELECT pg_reload_conf().
-- Для параллельных Hash Join нужен --shm-size не меньше ~1g
-- (дефолт Docker 64 МБ).

\echo '=== Параметры ДО ALTER SYSTEM ==='
SELECT name, setting, unit, source
FROM pg_settings
WHERE name IN (
    'shared_buffers', 'effective_cache_size', 'work_mem',
    'maintenance_work_mem', 'random_page_cost', 'effective_io_concurrency',
    'checkpoint_completion_target', 'max_wal_size', 'wal_buffers', 'jit'
)
ORDER BY name;

-- 1. Кэш страниц
-- tickets ~627 МБ, idx_tickets_sold_at ~309 МБ.
-- Дефолт образа postgres:12 — shared_buffers=128MB: покрывающий индекс
-- в shared buffers не влезает, EXPLAIN пишет десятки тысяч shared read
-- (страница не найдена в кэше сервера, чтение через OS cache/диск).
-- 512 МБ — индекс недели помещается целиком. На выделенном сервере
-- обычно берут ~25% RAM; здесь контейнер делит хост с другими сервисами.
ALTER SYSTEM SET shared_buffers = '512MB';

-- Подсказка планировщику, сколько данных ОС может держать в page cache.
-- Дефолт 4 ГБ на этой машине оставляем: RAM 15 ГБ, но занята другими
-- контейнерами, завышать effective_cache_size = врать планировщику.
ALTER SYSTEM SET effective_cache_size = '4GB';

-- 2. Память на одну операцию Sort / Hash Join / Hash Aggregate.
-- Это НЕ лимит на весь сервер: каждый worker берёт свой work_mem.
-- Дефолт 4 МБ: запрос 4 разлил Parallel Hash Join во временные файлы
-- (temp read/written ~17800 страниц ≈ 140 МБ). 64 МБ хватает, чтобы
-- hash по 400k сеансов собрался в 1 batch (~20 МБ по факту).
ALTER SYSTEM SET work_mem = '64MB';

-- VACUUM / CREATE INDEX по 8 млн строк. Дефолт 64 МБ.
ALTER SYSTEM SET maintenance_work_mem = '256MB';

-- 3. Модель стоимости планировщика
-- random_page_cost=4 — «диск как HDD»: случайное чтение в 4 раза дороже
-- последовательного, планировщик чаще выбирает Seq Scan. Данные на NVMe,
-- случайное чтение почти даром → 1.1 (типичное значение для SSD).
ALTER SYSTEM SET random_page_cost = '1.1';

-- Сколько concurrent I/O планировщик считает нормальным для bitmap/bitmap
-- heap. Дефолт 1 (один шпиндель). Для SSD — сотни.
ALTER SYSTEM SET effective_io_concurrency = '200';

-- 4. WAL и контрольные точки (вебинар «WAL»)
-- checkpoint_completion_target=0.5 сбрасывает грязные страницы за первую
-- половину интервала → пики I/O на наполнении и CREATE INDEX. 0.9 растягивает
-- запись. max_wal_size больше — реже чекпоинты при массовой загрузке.
ALTER SYSTEM SET checkpoint_completion_target = '0.9';
ALTER SYSTEM SET max_wal_size = '2GB';
ALTER SYSTEM SET wal_buffers = '16MB';

-- 5. JIT
-- На запросах 2 и 4 JIT компилировал 14–79 функций и добавлял 38–158 мс
-- к Execution Time. Для коротких/средних запросов (даже сканов на 8 млн)
-- компиляция не окупается. Выключаем на уровне сервера.
ALTER SYSTEM SET jit = 'off';

SELECT pg_reload_conf();

\echo '=== Параметры ПОСЛЕ reload (shared_buffers меняется только после рестарта) ==='
SELECT name, setting, unit, pending_restart
FROM pg_settings
WHERE name IN (
    'shared_buffers', 'effective_cache_size', 'work_mem',
    'maintenance_work_mem', 'random_page_cost', 'effective_io_concurrency',
    'checkpoint_completion_target', 'max_wal_size', 'wal_buffers', 'jit'
)
ORDER BY name;
