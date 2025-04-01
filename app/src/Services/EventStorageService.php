<?php

declare(strict_types=1);

namespace App\Services;

use App\Application;
use App\Repositories\EventStorageRepositoryInterface;
use DomainException;

/**
 * Class EventStorageService
 * @package App\Services
 */
class EventStorageService implements EventStorageServiceInterface
{
    /**
     * @var EventStorageRepositoryInterface
     */
    private EventStorageRepositoryInterface $repository;

    /**
     * @throws DomainException
     */
    public function __construct()
    {
        $this->repository = Application::$app->getComponent('storageRepository');
    }

    /**
     * @inheritdoc
     */
    public function clearAll(): void
    {
        $this->repository->clear();
    }
}
