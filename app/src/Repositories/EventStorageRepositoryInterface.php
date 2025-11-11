<?php

declare(strict_types=1);

namespace App\Repositories;

/**
 * Interface EventStorageRepositoryInterface
 * @package App\Repositories
 */
interface EventStorageRepositoryInterface
{
    /**
     * @return void
     */
    public function clear(): void;
}
