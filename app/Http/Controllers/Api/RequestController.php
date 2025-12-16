<?php declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Client Request API", version="1.0")
 */
class RequestController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/request",
     *     summary="Создать задачу",
     *     tags={"ClientRequests"},
     *     @OA\Response(
     *         response=200,
     *         description="Успешно создана задача",
     *         @OA\JsonContent(
     *             @OA\Property(property="requestId", type="string", example="d6c6e1f2-b3ae-4c17-bec9-132da48b519b")
     *         )
     *     )
     * )
     */
    public function submitRequest(Request $request): JsonResponse
    {
        $requestId = (string) Str::uuid();
        cache()->put("request_status_{$requestId}", 'pending');
        ProcessClientRequest::dispatch($requestId, $request->all());
        return response()->json(['request_id' => $requestId]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/request/{requestId}",
     *     summary="Получить статус задачи",
     *     tags={"ClientRequests"},
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Статус задачи",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Задача не найдена"
     *     )
     * )
     */
    public function checkStatus(string $requestId): JsonResponse
    {
        $status = cache()->get("request_status_{$requestId}", 'not_found');
        return response()->json(['status' => $status]);
    }
}
