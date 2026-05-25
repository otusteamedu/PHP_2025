<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\Email;

use App\Controller\Http\AbstractController;
use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Domain\EmailVerification\EmailVerifier;

class EmailController extends AbstractController
{
    public function __construct(
        private readonly Request $request,
        private readonly EmailVerifier $emailVerifier,
    ) {
    }

    public function verifyEmails(): Response
    {
        try {
            $payload = $this->request->getPayload();
            if (!isset($payload['emails'])) {
                throw new \Exception('Emails parameter is not passed in request body', 400);
            }
            $response = $this->emailVerifier->getValidEmails($payload['emails']);
            $httpCode = 200;
        } catch (\Exception $e) {
            $httpCode = $e->getCode();
            $response = $e->getMessage();
        }

        return $this->json(['response' => $response], $httpCode);
    }
}
