<?php
declare(strict_types=1);


namespace App\Domain\Repository;

interface BookRepositoryInterface
{
    public function dbCreate(string $dbTitle, ?array $mappings = null, ?array $settings = null): bool;

    public function dbDelete(string $dbTitle): bool;

}