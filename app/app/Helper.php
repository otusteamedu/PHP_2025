<?php

namespace App;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Helper
{
    public const REQUEST_SUCCESS_STATUS = 'ok';
    public const REQUEST_ERROR_STATUS = 'error';

    public static function successResponse($data = [], string $message = 'Success server`s response', $responseCode = false): JsonResponse
    {
        $responseCode = ($responseCode) ? $responseCode: Response::HTTP_OK;
        return response()->json([
            'status' => self::REQUEST_SUCCESS_STATUS,
            'message' => $message,
            'info' => $data
        ], $responseCode);
    }

    public static function failedResponse(string $errorMessage = 'error', $trace = '', $responseCode = false): JsonResponse
    {
        $responseCode = ($responseCode) ? $responseCode: Response::HTTP_INTERNAL_SERVER_ERROR;
        return response()->json([
            'status' => self::REQUEST_ERROR_STATUS,
            'message' => $errorMessage,
            'trace' => $trace
        ], $responseCode);
    }

}
