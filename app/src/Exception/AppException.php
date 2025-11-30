<?php
namespace App\Exception;

use App\Http\JsonResponse;

class AppException extends \Exception 
{

    public function __construct(string $message = "", private int $statusCode = 500)
    {
        parent::__construct($message);
    }

    // Возврат ответа без оптправки
    public function toResponse(): JsonResponse
    {
        return new JsonResponse(['error'=>$this->getMessage()], $this->statusCode);
    }
}
