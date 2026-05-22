<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\JsonRequestReader;
use App\Http\JsonResponse;
use App\Queue\QueuePublisherInterface;
use App\Repository\RequestRepository;
use App\Service\RequestService;

/**
 * Контроллер запросов на обработку
 */
final class RequestController
{
    private readonly RequestService $service;
    private readonly JsonRequestReader $requestReader;
    private readonly JsonResponse $response;

    public function __construct(RequestRepository $repository, QueuePublisherInterface $publisher)
    {
        $this->service = new RequestService($repository, $publisher);
        $this->requestReader = new JsonRequestReader();
        $this->response = new JsonResponse();
    }

    /**
     * Создает запрос на обработку
     *
     * @return void
     */
    public function create(): void
    {
        $payload = $this->requestReader->read();
        $request = $this->service->create($payload);

        $this->response->send(['request_id' => $request->getId()]);
    }

    /**
     * Возвращает запрос на обработку по идентификатору
     *
     * @param int $requestId Идентификатор запроса
     *
     * @return void
     */
    public function get(int $requestId): void
    {
        $request = $this->service->get($requestId);

        $this->response->send($request->toArray());
    }
}
