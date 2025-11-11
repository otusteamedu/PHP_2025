<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Application;
use App\Forms\EventSearch;
use App\Http\Exceptions\BadRequestHttpException;
use App\Http\Exceptions\MethodNotAllowedHttpException;
use App\Http\Response;
use App\Models\Event;
use App\Services\EventService;
use App\Services\EventServiceInterface;
use DomainException;
use Throwable;

/**
 * Class EventController
 * @package App\Controllers
 */
class EventController extends BaseController
{
    /**
     * @var EventServiceInterface
     */
    private EventServiceInterface $service;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = new EventService();
    }

    /**
     * @return Response
     * @throws DomainException
     * @throws MethodNotAllowedHttpException
     * @throws BadRequestHttpException
     */
    public function actionCreate(): Response
    {
        $request = Application::$app->getRequest();
        if (!$request->isPost()) {
            throw new MethodNotAllowedHttpException('Only POST method allowed');
        }

        $requestRawBody = $request->getRawBody();
        if (!$requestRawBody) {
            throw new BadRequestHttpException('Request body must not be empty');
        }

        try {
            $event = Event::createFromJson($requestRawBody);

            $this->service->create($event);

            return $this->asJson([
                'message' => 'Event successfully created',
            ]);
        } catch (Throwable $e) {
            throw new DomainException('Error creating event: ' . $e->getMessage());
        }
    }

    /**
     * @return Response
     * @throws DomainException
     * @throws BadRequestHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionSearch(): Response
    {
        $request = Application::$app->getRequest();
        if (!$request->isGet()) {
            throw new MethodNotAllowedHttpException('Only GET method allowed');
        }

        $requestRawBody = $request->getRawBody();
        if (!$requestRawBody) {
            throw new BadRequestHttpException('Request body must not be empty');
        }

        try {
            $eventSearch = EventSearch::createFromJson($requestRawBody);
            $event = $this->service->search($eventSearch);

            if (!$event) {
                return $this->asJson([
                    'message' => 'No events found',
                ]);
            }

            return $this->asJson($event->toArray());
        } catch (Throwable $e) {
            throw new DomainException('Error searching events: ' . $e->getMessage());
        }
    }
}
