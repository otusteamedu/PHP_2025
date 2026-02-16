<?php

declare(strict_types=1);

namespace Ak\Hw\Infrastructure;

class ApiService
{
    public function charge(array $orderDetails): bool
    {
        // Имитация запроса к внешнему сервису A
        // В реальном приложении здесь будет использоваться Guzzle или другая HTTP-клиентская библиотека
        
        // Для теста, предположим, что сервис A всегда успешен
        // Чтобы протестировать ошибку, можно изменить это значение
        $success = true; 

        if (!$success) {
            throw new \Exception('Payment failed on service A');
        }

        return true;
    }
}
