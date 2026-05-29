<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Entity\FallbackMessage;

/**
 * Интерфейс репозитория неудачных сообщений из DLQ
 */
interface FallbackMessageRepositoryInterface
{
    /**
     * Сохраняет неудачное сообщение для ручного разбора
     */
    public function save(FallbackMessage $message): FallbackMessage;
}
