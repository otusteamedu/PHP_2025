<?php declare(strict_types=1);

namespace App\Services;

/**
 * Interface ShopStorageServiceInterface
 * @package App\Services
 */
interface ShopStorageServiceInterface
{
    /**
     * @return array
     */
    public function create(): array;

    /**
     * @param string $items
     * @return array
     */
    public function seed(string $items): array;

    /**
     * @return array
     */
    public function delete(): array;
}