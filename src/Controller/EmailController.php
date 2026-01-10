<?php
namespace Aovchinnikova\Hw15\Controller;

use Aovchinnikova\Hw15\Service\EmailValidationService;
use Exception;

class EmailController
{
    private $emailValidationService;

    public function __construct()
    {
        $this->emailValidationService = new EmailValidationService();
    }

    /**
     * @return void
     */
    public function handleValidationRequest()
    {
        try {
            $requestPayload = file_get_contents('php://input');
            $parsedInput = json_decode($requestPayload, true);

            if ($parsedInput === null || !isset($parsedInput['emails'])) {
                throw new Exception('Invalid JSON or missing "emails" field.');
            }

            $results = $this->validateEmails($parsedInput['emails']);

            header('Content-Type: application/json');
            echo json_encode($results);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param array $emails
     * @return array
     */
    private function validateEmails(array $emails): array
    {
        $results = [];
        foreach ($emails as $email) {
            $results[$email] = $this->emailValidationService->validate($email);
        }

        return $results;
    }
}