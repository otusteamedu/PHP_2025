<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Интерфейс параметров поиска
 */
interface SearchParamsInterface
{
    /**
     * Проверяет, что хотя бы один параметр поиска задан
     */
    public function hasSearchCriteria(): bool;
}

