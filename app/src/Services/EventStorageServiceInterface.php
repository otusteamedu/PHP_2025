<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Interface EventStorageServiceInterface
 * @package App\Services
 */
interface EventStorageServiceInterface
{
    /**
     * @return void
     */
    public function clearAll(): void;
}
