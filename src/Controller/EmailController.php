<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Response;
use App\Service\EmailVerifier;

class EmailController extends AbstractController
{
    public function verifyEmails(): Response
    {
        try {
            $payload = $this->request->getPayload();
            if (!isset($payload['emails'])) {
                throw new \Exception('Emails parameter is not passed in request body', 400);
            }
            $response = new EmailVerifier($payload['emails'])->getValidEmails();
            $httpCode = 200;
        } catch (\Exception $e) {
            $httpCode = $e->getCode();
            $response = $e->getMessage();
        }

        return new Response(
            json_encode(['response' => $response]),
            $httpCode,
            ['Content-Type: application/json; charset=utf-8'],
        );
    }
}
