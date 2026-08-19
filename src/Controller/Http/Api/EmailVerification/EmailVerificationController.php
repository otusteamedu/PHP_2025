<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\EmailVerification;

use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Exception\HttpExceptionInterface;
use App\Core\Http\Exception\InvalidPayloadTypeException;
use App\Core\Http\Exception\MissingPayloadFieldException;
use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Domain\EmailVerification\EmailVerifier;

class EmailVerificationController extends AbstractController
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
            $emails = $this->extractAndValidateEmails($payload);
            $validEmails = $this->emailVerifier->getValidEmails($emails);

            return $this->json([
                'success' => true,
                'validEmails' => $validEmails,
            ]);

        } catch (HttpExceptionInterface $e) {
            return $this->json(
                [
                    'success' => false,
                    'error' => $e->getMessage(),
                ],
                $e->getHttpCode(),
            );
        }
    }

    /**
     * @throws MissingPayloadFieldException
     * @throws InvalidPayloadTypeException
     */
    private function extractAndValidateEmails(mixed $payload): array
    {
        if (!isset($payload['emails'])) {
            throw new MissingPayloadFieldException('Missing required field: emails', 400);
        }

        $emails = $payload['emails'];

        if (!is_array($emails)) {
            throw new InvalidPayloadTypeException('Field "emails" must be an array of strings', 400);
        }

        if (!array_all($emails, fn($email) => is_string($email))) {
            throw new InvalidPayloadTypeException('Field "emails" must contain only strings', 400);
        }

        return $emails;
    }
}
