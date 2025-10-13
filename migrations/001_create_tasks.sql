CREATE TABLE IF NOT EXISTS public.tasks (
    id TEXT PRIMARY KEY,
    payload TEXT NULL,
    status TEXT NOT NULL DEFAULT 'queued',
    processed_at TIMESTAMPTZ NULL
);
CREATE INDEX IF NOT EXISTS idx_tasks_status ON public.tasks(status);
