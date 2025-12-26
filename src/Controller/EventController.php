<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Model\Event;
use App\Repository\EventRepositoryInterface;
use App\Service\EventMatchingService;

class EventController
{
    private EventRepositoryInterface $repository;

    private EventMatchingService $matchingService;

    private Request $request;

    /**
     * @param EventRepositoryInterface $repository
     * @param EventMatchingService $matchingService
     * @param Request $request
     */
    public function __construct(
        EventRepositoryInterface $repository,
        EventMatchingService $matchingService,
        Request $request
    ) {
        $this->repository = $repository;
        $this->matchingService = $matchingService;
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function handle(): Response
    {
        try {
            $response = match (true) {
                $this->request->method() === 'POST' && $this->request->path() === '/events' => $this->addEvent(),
                $this->request->method() === 'DELETE' && $this->request->path() === '/events' => $this->clearEvents(),
                $this->request->method() === 'POST' && $this->request->path() === '/match' => $this->matchEvent(),
                default => $this->notFound()
            };
        } catch (\Throwable $e) {
            $response = Response::json([
                'status' => 'error',
                'message' => 'Internal server error',
            ], 500);
        }

        return $response;
    }

    /**
     * @return Response
     */
    private function addEvent(): Response
    {
        $data = $this->request->json();

        if (!isset($data['priority'], $data['conditions'], $data['event'])) {
            return Response::json([
                'status' => 'error',
                'message' => 'Missing required fields',
            ], 400);
        }

        $event = new Event((int)$data['priority'], $data['conditions'], $data['event']);
        $this->repository->save($event);

        return Response::json([
            'status' => 'success',
            'data' => $event->toArray(),
        ], 201);
    }

    /**
     * @return Response
     */
    private function clearEvents(): Response
    {
        $this->repository->clear();

        return Response::json([
            'status' => 'success',
            'message' => 'Events cleared',
        ]);
    }

    /**
     * @return Response
     */
    private function matchEvent(): Response
    {
        $data = $this->request->json();

        if (!isset($data['params'])) {
            return Response::json([
                'status' => 'error',
                'message' => 'Missing params field',
            ], 400);
        }

        $matchedEvent = $this->matchingService->findBestMatch($data['params']);

        return Response::json([
            'status' => 'success',
            'data' => $matchedEvent?->toArray(),
        ]);
    }

    /**
     * @return Response
     */
    private function notFound(): Response
    {
        return Response::json([
            'status' => 'error',
            'message' => 'Endpoint not found',
        ], 404);
    }
}
