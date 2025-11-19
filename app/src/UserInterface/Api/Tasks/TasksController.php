<?php

declare(strict_types=1);

namespace App\UserInterface\Api\Tasks;

use App\Application\CreateTask\CreateTaskHandler;
use App\Application\CreateTask\CreateTaskQuery;
use App\Application\GetTask\GetTaskHandler;
use App\Application\GetTask\GetTaskQuery;
use App\UserInterface\Api\Tasks\CreateTask\CreateTaskRequest;
use App\UserInterface\Api\Tasks\CreateTask\CreateTaskResponse;
use App\UserInterface\Api\Tasks\GetTask\GetTaskResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class TasksController
{
    public function __construct(
        private CreateTaskHandler $createTaskHandler,
        private GetTaskHandler $getTaskHandler,
    ){

    }
    #[Route('/task', name: 'send_task', methods: ['POST'])]
    public function createTask(
        #[MapRequestPayload] CreateTaskRequest $request
    ): Response
    {
        $query = new CreateTaskQuery(
            email: $request->email,
            title: $request->title,
            description: $request->description,
        );

        $output = $this->createTaskHandler->__invoke($query);

        return new JsonResponse(new CreateTaskResponse($output->id));

    }

    #[Route('/task/{id}', name: 'get_task', methods: ['GET'])]
    public function getTask(
        string $id,
    ): Response
    {
        $query = new GetTaskQuery($id);
        $output = $this->getTaskHandler->__invoke($query);

        $response = new GetTaskResponse(
            title: $output->title,
            description: $output->description,
            email: $output->email,
            status: $output->status,
        );

        return new JsonResponse($response);
    }

}
