<?php

declare(strict_types=1);

namespace App\Core\Http\Controller\Base;

use App\Core\Http\Message\Response;
use Customer41\MultiException\MultiException;

trait ApiResponseTrait
{
    private function apiResponse(callable $operation): Response
    {
        try {
            $responseData = $operation();
            return $this->json(['success' => true, ...$responseData]);
        } catch (MultiException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    private function validationErrorResponse(MultiException $validationErrors): Response
    {
        $errors = [];
        foreach ($validationErrors->getIterator() as $error) {
            $errors[] = $error->getMessage();
        }

        $data = [
            'success' => false,
            'message' => $validationErrors->getMessage(),
            'details' => $errors,
        ];

        return $this->json($data, $validationErrors->getCode());
    }

    private function errorResponse(\Throwable $e): Response
    {
        $data = [
            'success' => false,
            'message' => $e->getMessage(),
        ];

        return $this->json($data, $e->getCode() ?: 500);
    }
}
