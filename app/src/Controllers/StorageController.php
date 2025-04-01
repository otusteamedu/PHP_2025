<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;
use App\Services\EventStorageService;
use App\Services\EventStorageServiceInterface;
use DomainException;
use Throwable;

/**
 * Class StorageController
 * @package App\Controllers
 */
class StorageController extends BaseController
{
    /**
     * @var EventStorageServiceInterface
     */
    private EventStorageServiceInterface $service;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = new EventStorageService();
    }

    /**
     * @return Response
     * @throws DomainException
     */
    public function actionClear(): Response
    {
        try {
            $this->service->clearAll();
            return $this->asJson([
                'message' => 'Events successfully cleared',
            ]);
        } catch (Throwable $e) {
            throw new DomainException('Error clearing events: ' . $e->getMessage());
        }
    }
}
