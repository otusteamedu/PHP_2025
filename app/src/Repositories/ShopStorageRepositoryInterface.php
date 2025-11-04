<?php declare(strict_types=1);

namespace App\Repositories;

/**
 * Interface ShopStorageRepository
 * @package App\Repositories
 */
interface ShopStorageRepositoryInterface
{
    /**
     * @return array
     */
    public function create(): array;

    /**
     * @param string $items
     * @return array
     */
    public function addItems(string $items): array;

    /**
     * @return array
     */
    public function delete(): array;
}
