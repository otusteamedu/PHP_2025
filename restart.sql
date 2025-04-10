TRUNCATE TABLE clients, films, orders, rooms, session, tickets CASCADE;

DO $$
DECLARE
seq RECORD;
BEGIN
    -- Для каждой последовательности, связанной с автоинкрементными полями
FOR seq IN
SELECT sequence_name
FROM information_schema.sequences
WHERE sequence_schema = 'public'  -- если последовательности находятся в схеме public
    LOOP
        EXECUTE 'ALTER SEQUENCE ' || seq.sequence_name || ' RESTART WITH 1';
END LOOP;
END $$;