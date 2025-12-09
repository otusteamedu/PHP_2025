<?php

declare(strict_types=1);

namespace App;

class SessionManager
{
    /**
     * Безопасный старт сессии с повторными попытками при временных сбоях Redis
     *
     * @param int $maxAttempts Максимальное количество попыток
     * @param int $retryDelayMs Задержка между попытками в миллисекундах
     * @return bool true если сессия успешно запущена, false в противном случае
     */
    public function start(int $maxAttempts = 5, int $retryDelayMs = 200): bool
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                if (session_start()) {
                    return true;
                }
            } catch (\RedisClusterException $e) {
                error_log(sprintf(
                    'RedisClusterException (attempt %d/%d): %s',
                    $attempt,
                    $maxAttempts,
                    $e->getMessage()
                ));
            }
            
            if ($attempt < $maxAttempts) {
                usleep($retryDelayMs * 1000);
            }
        }
        
        return false;
    }

    /**
     * Обновляет данные сессии
     *
     * @return array Данные сессии
     */
    public function updateData(): array
    {
        $_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;
        $_SESSION['last_visit_at'] = $_SESSION['last_visit_at'] ?? date('c');
        $_SESSION['container'] = $_SERVER['HOSTNAME'] ?? '-';
        $_SESSION['session_id'] = session_id() ?: '-';

        return $_SESSION;
    }
}
