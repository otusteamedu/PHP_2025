<?php

declare(strict_types=1);

namespace Otus\Queue\Application\UseCase\Chat;

use Otus\Queue\Application\Interface\ChatRepositoryInterface;

final readonly class History
{
    /**
     * @param ChatRepositoryInterface $repository
     */
    public function __construct(
        private ChatRepositoryInterface $repository
    ) {
    }

    /**
     * @return array
     */
    public function handle(): array
    {
        return $this->repository->history();
    }
}
