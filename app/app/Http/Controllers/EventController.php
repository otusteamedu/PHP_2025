<?php

namespace App\Http\Controllers;


use App\Helper;
use App\Services\EventService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private EventService $eventService;


    public function __construct()
    {
        $this->eventService = new EventService();
    }

     /**
     * @OA\Post(
     *     path="/add-event",
     *     summary="Add new Event",
     *     tags={"AddEvent"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add new event",
     *         @OA\JsonContent(required={"number"},@OA\Property(property="number", type="integer"),
     *     ),
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="New event added to queue",
     *         @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error: 500 Internal Server Error. When event with such number has already exists or parameters are wrong or were not supplied",
     *     )
     * )
     * @param Request $request
     * @return array
     */
    public function add(Request $request) {
        try {
            $this->validate($request, ['number' => 'required|integer']);
            event(new \App\Events\AddUserRequestEvent($request->number));
            return Helper::successResponse([], 'New event added to queue');

        } catch(\Exception $exception) {
            return Helper::failedResponse($exception->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/get-event/{number}",
     *     tags={"GetEvent"},
     *     @OA\Parameter(
     *         name="number",
     *         in="path",
     *         description="The number of user event",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample category things",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error: 500 Internal Server Error. When required parameters are wrong or were not supplied.",
     *     )
     * )
     */
    public function get(int $number) {
        try {
            $event = $this->eventService->getEvent($number);
            if (!$event) {
                throw new \RuntimeException('Event with such number not found');
            }

            return Helper::successResponse([
                'event' => $event,
            ], 'Event');

        } catch(\Exception $exception) {
            return Helper::failedResponse($exception->getMessage());
        }
    }

}
