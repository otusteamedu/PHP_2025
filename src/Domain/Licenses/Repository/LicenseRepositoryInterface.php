<?php
declare(strict_types=1);

namespace Domain\Licenses\Repository;

use Domain\Licenses\Entity\License;

interface LicenseRepositoryInterface
{
    /**
     * @return License[]
     */
    public function findAll(): iterable;

    public function findById(int $id): ?License;

    public function save(License $license): void;

    public function delete(License $license): void;
}