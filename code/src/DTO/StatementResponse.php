<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * DTO с результатом генерации банковской выписки.
 */
final class StatementResponse
{
    /**
     * @param string $subject Тема сообщения с выпиской.
     * @param string $body Текст выписки.
     * @param string $generatedAt Дата формирования выписки.
     */
    public function __construct(
        public readonly string $subject,
        public readonly string $body,
        public readonly string $generatedAt,
    ) {
    }
}
