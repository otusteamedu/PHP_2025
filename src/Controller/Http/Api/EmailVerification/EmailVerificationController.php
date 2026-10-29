<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\EmailVerification;

use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Exception\HttpExceptionInterface;
use App\Core\Http\Exception\FieldValidationException;
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
     * @throws FieldValidationException
     */
    private function extractAndValidateEmails(mixed $payload): array
    {
        if (!isset($payload['emails'])) {
            throw new FieldValidationException('emails', 'Field "emails" is required');
        }

        $emails = $payload['emails'];

        if (!is_array($emails)) {
            throw new FieldValidationException('emails', 'Field "emails" must be an array of strings');
        }

        if (!array_all($emails, fn($email) => is_string($email))) {
            throw new FieldValidationException('emails', 'Field "emails" must contain only strings');
        }

        return $emails;
    }
}
