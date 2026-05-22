<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Статусы обработки запроса
 */
enum RequestStatus: string
{
    case Queued = 'queued';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    /**
     * Возвращает название статуса на русском языке
     *
     * @return string
     */
    public function getTitle(): string
    {
        return match ($this) {
            self::Queued => 'В очереди',
            self::Processing => 'В обработке',
            self::Completed => 'Обработан',
            self::Failed => 'Ошибка обработки',
        };
    }
}
