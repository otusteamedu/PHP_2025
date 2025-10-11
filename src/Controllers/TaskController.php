<?php declare(strict_types=1);

namespace App\Tasks\Controllers;

use Monolog\Logger;
use App\Tasks\Models\Task;
use Monolog\Handler\StreamHandler;
use App\Tasks\Services\TaskService;
use App\Tasks\Services\RabbitMQService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @OA\Info(
 *     title="Task Queue API",
 *     version="1.0.0",
 *     description="API for managing tasks through RabbitMQ queue"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Development Server"
 * )
 *
 * @OA\Tag(
 *     name="Tasks",
 *     description="Task management operations"
 * )
 *
 * @OA\Tag(
 *     name="Info",
 *     description="API information endpoints"
 * )
 */
class TaskController
{
	private TaskService $taskService;
	private RabbitMQService $rabbitMQService;
	private Logger $logger;

	public function __construct()
	{
		$this->taskService = new TaskService();
		$this->rabbitMQService = new RabbitMQService();

		$this->logger = new Logger('TaskController');
		$this->logger->pushHandler(new StreamHandler('php://stdout'));
	}

	/**
	 * @OA\Post(
	 *     path="/tasks",
	 *     summary="Create a new task",
	 *     description="Create a new task and add it to the processing queue",
	 *     tags={"Tasks"},
	 *     @OA\RequestBody(
	 *         required=true,
	 *         description="Task data",
	 *         @OA\JsonContent(
	 *             required={"data"},
	 *             @OA\Property(
	 *                 property="data",
	 *                 type="object",
	 *                 description="Task data to be processed",
	 *                 example={"type": "image_processing", "file": "image.jpg"}
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=201,
	 *         description="Task created successfully",
	 *         @OA\JsonContent(ref="#/components/schemas/Task")
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid input"
	 *     ),
	 *     @OA\Response(
	 *         response=500,
	 *         description="Server error"
	 *     )
	 * )
	 */
	public function createTask(Request $request, Response $response): Response
	{
		$data = json_decode($request->getBody()->getContents(), true);

		$task = $this->taskService->createTask($data['data'] ?? []);

		// Отправляем задачу в очередь
		try {
			$this->rabbitMQService->publishMessage([
				'task_id' => $task->id,
				'data' => $task->data
			]);
			$this->logger->info("Message successfully sent to RabbitMQ.");
		} catch (\Exception $e) {
			$this->logger->info("No publish message: {$e}");
		}

		$response->getBody()->write(json_encode($task->toArray()));
		return $response
			->withHeader('Content-Type', 'application/json')
			->withStatus(201);
	}

	/**
	 * @OA\Get(
	 *     path="/tasks/{id}",
	 *     summary="Get task status",
	 *     description="Retrieve the current status of a task by its ID",
	 *     tags={"Tasks"},
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         required=true,
	 *         description="Task ID",
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Task details retrieved successfully",
	 *         @OA\JsonContent(ref="#/components/schemas/Task")
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Task not found",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="error", type="string", example="Task not found")
	 *         )
	 *     )
	 * )
	 */
	public function getTask(Request $request, Response $response, array $args): Response
	{
		$taskId = $args['id'];
		$task = $this->taskService->getTask($taskId);

		if (!$task) {
			$response->getBody()->write(json_encode(['error' => 'Task not found']));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(404);
		}

		$response->getBody()->write(json_encode($task->toArray()));
		return $response
			->withHeader('Content-Type', 'application/json')
			->withStatus(200);
	}
}