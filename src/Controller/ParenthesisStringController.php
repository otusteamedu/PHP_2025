<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Response;
use App\Validator\ParenthesisStringValidator;

class ParenthesisStringController extends AbstractController
{
    public function verifyParenthesisString(): Response
    {
        try {
            $payload = $this->request->getPayload();
            if (!isset($payload['string'])) {
                throw new \Exception('String parameter is not passed in request body', 400);
            }
            $isValidStr = ParenthesisStringValidator::isValid($payload['string']);
            $httpCode = $isValidStr ? 200 : 400;
            $message = $isValidStr ? 'Всё хорошо' : 'Всё плохо';
        } catch (\Exception $e) {
            $httpCode = $e->getCode();
            $message = $e->getMessage();
        }

        return new Response(
            json_encode(['response' => $message]),
            $httpCode,
            ['Content-Type: application/json; charset=utf-8'],
        );
    }
}
