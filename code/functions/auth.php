<?php

declare(strict_types=1);

/**
 * Безопасный старт сессии с повторными попытками при временных сбоях Redis
 *
 * @param int $maxAttempts Максимальное количество попыток
 * @param int $retryDelayMs Задержка между попытками в миллисекундах
 * @return bool true если сессия успешно запущена, false в противном случае
 */
function startSessionSafe(int $maxAttempts = 3, int $retryDelayMs = 100): bool
{
    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        if (@session_start()) {
            return true;
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
function updateSessionData(): array
{
    $_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;
    $_SESSION['last_visit_at'] = $_SESSION['last_visit_at'] ?? date('c');
    $_SESSION['container'] = $_SERVER['HOSTNAME'] ?? '-';
    $_SESSION['session_id'] = session_id() ?: '-';

    return $_SESSION;
}
