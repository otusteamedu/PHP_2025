<?php

class SessionManager
{
    public function __construct()
    {
        ini_set('session.save_handler', 'rediscluster');
        ini_set('session.save_path', 'seed[]=redis-1:6379&seed[]=redis-2:6379&seed[]=redis-3:6379');
        session_start();
    }

    /**
     * Обновляет счетчик запросов и сохраняет имя хоста в сессии.
     */
    public function updateSessionData(): void
    {
        $count = isset($_SESSION['count']) ? $_SESSION['count'] + 1 : 1;
        $_SESSION['count'] = $count;
        $_SESSION['hostname'] = gethostname();
    }

    /**
     * Возвращает информацию из сессии
     */
    public function getSessionInfo(): array
    {
        $hostname = $_SESSION['hostname'] ?? 'N/A';
        $count = $_SESSION['count'] ?? 0;

        return [
            'hostname' => $hostname,
            'count' => $count,
        ];
    }
}
