SELECT 'CREATE DATABASE database_patterns'
WHERE NOT EXISTS (
    SELECT FROM pg_database WHERE datname = 'database_patterns'
)
\gexec
