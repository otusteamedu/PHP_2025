<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\EmailVerificationService;
use App\Validator\EmailValidator;

class EmailVerificationController
{
    private EmailVerificationService $emailVerificationService;

    /**
     * @param EmailVerificationService $emailVerificationService
     */
    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        return $request->isPost()
            ? $this->handlePost($request)
            : $this->handleGet();
    }

    /**
     * @param Request $request
     * @return Response
     */
    private function handlePost(Request $request): Response
    {
        $rawInput = $request->getEmailsInput();
        $emails = $this->parseEmails($rawInput);
        $results = $this->emailVerificationService->verifyMultiple($emails);

        return Response::json($results);
    }

    /**
     * @return Response
     */
    private function handleGet(): Response
    {
        return Response::view(__DIR__ . '/../View/verify.html');
    }

    /**
     * @param string $input
     * @return array
     */
    private function parseEmails(string $input): array
    {
        $emails = array_map('trim', explode(PHP_EOL, trim($input)));
        return array_filter($emails);
    }

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self(new EmailVerificationService(new EmailValidator()));
    }
}
