<?php

namespace Infrastructure\Http\Controllers;

use Application\Event\EventCreateUseCase;
use Application\Event\EventGetUseCase;
use Application\Event\EventOneUseCase;
use Application\Http\Controller\Controller;
use Application\Http\Response;
use Application\Queue\RabbitMQ;
use Domain\Entity\Event;
use Infrastructure\Http\Transformers\EventTransformer;
use Infrastructure\Http\Validators\EventControllerValidator;
use Infrastructure\Repositories\EventRepository;

/**
 * @OA\Info(title="Контроллер событий", version="0.1")
 */
class EventController extends Controller
{
    /** @var string|null */
    protected ?string $validator = EventControllerValidator::class;

    /**
     * @OA\Get(
     *     path="/event",
     *     tags={"Events"},
     *     summary="Получить список Событий",
     *     @OA\Response(
     *         response=200,
     *         description="Список Событий",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="/components/schemas/Event")
     *         )
     *     )
     * )
     */
    public function get(): Response {
        $events = (new EventGetUseCase(new EventRepository()))->run();

        return new Response(
            EventTransformer::transform($events),
            200
        );
    }


    /**
     * @OA\Get(
     *     path="/event/one",
     *     tags={"Events"},
     *     summary="Получить Событие",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID события для выдачи строки из таблицы События",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Событие по id",
     *         @OA\JsonContent(ref="/components/schemas/Event"),
     *     )
     * )
     */
    public function one(): Response {
        $data = $this->request->getQuery();

        $event = (new EventOneUseCase(new EventRepository()))->run($data['id']);

        return new Response(
            EventTransformer::transform([$event])[0],
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/event",
     *     tags={"Events"},
     *     summary="Создать Событие",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             examples={
     *                 @OA\Examples(example="PostEventCreation", summary="Данные для создания События", value={"type":"create","title"="Какой-то тайтл","priority":33,"comment":"Какой-то коммент"}),
     *             }
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Событие cоздано",
     *         @OA\JsonContent(ref="/components/schemas/Event"),
     *     )
     * )
     */
    public function create(): Response {
        $data = $this->request->getData();

        $event = new Event(
            null,
            $data['type'],
            $data['title'],
            $data['priority'],
            $data['comment']
        );

        (new EventCreateUseCase(
            new EventRepository(),
            new RabbitMQ()
        ))->run($event);

        return new Response([], 201, "Событие создано");
    }

    /**
     * @OA\Delete(
     *     path="/event",
     *     tags={"Events"},
     *     summary="Удалить Событие",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID события для удаления строки из таблицы События",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Событие удалено",
     *         @OA\JsonContent(ref="/components/schemas/Event"),
     *     )
     * )
     */
    public function delete(): Response {
        return new Response([], 200, "Событие было удалено");
    }
}