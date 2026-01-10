<?php
namespace App\Controller;

use App\Infrastructure\RabbitConnection;
use App\Repository\TaskRepository;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;

class TaskController
{
    private RabbitConnection $rabbit;
    private TaskRepository $repo;

    public function __construct(RabbitConnection $rabbit, TaskRepository $repo)
    {
        $this->rabbit = $rabbit;
        $this->repo = $repo;
    }

    /**
     * @OA\Post(
     *   path="/tasks",
     *   summary="Поставить задачу в очередь",
     *   description="Создаёт новый запрос на обработку и возвращает идентификатор запроса.",
     *   tags={"tasks"},
     *   @OA\RequestBody(
     *     required=false,
     *     @OA\MediaType(mediaType="application/json",
     *       @OA\Schema(
     *         type="object",
     *         @OA\Property(property="payload", type="string", description="Произвольные данные для обработки")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Задача поставлена в очередь",
     *     @OA\JsonContent(
     *       @OA\Property(property="id", type="string", example="c2a7a0ce-8c72-4e9e-bf44-b8481c9d9d1b"),
     *       @OA\Property(property="status", type="string", example="queued")
     *     )
     *   )
     * )
     * @throws \Exception
     */
    public function create(Request $request, Response $response): Response
    {
        $body = (string)$request->getBody();
        $json = $body !== '' ? json_decode($body, true) : [];
        $payload = is_array($json) && isset($json['payload']) ? (string)$json['payload'] : null;

        $id = Uuid::uuid4();

        // Сохраняем запись как queued (чтобы статус появился сразу)
        $this->repo->create($id, $payload);

        // Публикуем сообщение в RabbitMQ
        [$connection, $channel] = $this->rabbit->channel();
        $msg = new AMQPMessage(json_encode(['id' => $id, 'payload' => $payload ?? null]), [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]);
        $channel->basic_publish($msg, '', $this->rabbit->getQueueName());
        $channel->close();
        $connection->close();

        $response->getBody()->write(json_encode(['id' => $id, 'status' => 'queued'], JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(201);
    }

    /**
     * @OA\Get(
     *   path="/tasks/{id}",
     *   summary="Получить статус задачи",
     *   description="Возвращает текущий статус задачи и время обработки, если доступно.",
     *   tags={"tasks"},
     *   @OA\Parameter(name="id", in="path", required=true, description="Идентификатор задачи", @OA\Schema(type="string")),
     *   @OA\Response(
     *     response=200,
     *     description="Текущий статус",
     *     @OA\JsonContent(
     *       @OA\Property(property="id", type="string"),
     *       @OA\Property(property="status", type="string", example="queued"),
     *       @OA\Property(property="processed_at", type="string", nullable=true, example="2025-10-07T13:45:00+03:00")
     *     )
     *   ),
     *   @OA\Response(response=404, description="Задача не найдена")
     * )
     */
    public function status(Request $request, Response $response, array $args): Response
    {
        $id = (string)$args['id'];
        $task = $this->repo->get($id);
        if (!$task) {
            $response->getBody()->write(json_encode(['error' => 'Задача не найдена'], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus(404);
        }
        $response->getBody()->write(json_encode($task, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    }
}
