<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification;

use App\Controller\Http\Api\OrderNotification\Presenter\NotificationTaskPresenter;
use App\Controller\Http\Api\OrderNotification\Validator\OrderNotificationValidator;
use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Message\HttpStatus;
use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Domain\OrderNotification\NotificationTaskService;
use App\Domain\OrderNotification\ValueObject\Task\TaskId;

class OrderNotificationController extends AbstractController
{
    public function __construct(
        private readonly Request $request,
        private readonly OrderNotificationValidator $validator,
        private readonly NotificationTaskService $taskService,
        private readonly NotificationTaskPresenter $taskPresenter,
    ) {
    }

    public function createNotificationTask(): Response
    {
        $payload = $this->request->getPayload();
        $command = $this->validator->validate($payload);
        $task = $this->taskService->createTask($command);

        return $this->json([
            'success' => true,
            'data' => $this->taskPresenter->toArray($task),
        ], HttpStatus::Accepted->value);
    }

    public function getNotificationTaskStatus(): Response
    {
        $taskId = $this->request->getIntQueryParam('id');
        $task = $this->taskService->getTask(TaskId::fromInt($taskId));

        return $this->json([
            'success' => true,
            'data' => $this->taskPresenter->toArray($task),
        ], HttpStatus::Ok->value);
    }
}
