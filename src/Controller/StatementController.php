<?php

namespace App\Controller;

use App\Message\StatementRequestMessage;
use App\Service\QueueServiceInterface;

class StatementController
{
    private QueueServiceInterface $queueService;

    public function __construct(QueueServiceInterface $queueService)
    {
        $this->queueService = $queueService;
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method not allowed";
            return;
        }

        $email = $_POST['email'] ?? '';
        $accountNumber = $_POST['account_number'] ?? '';
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';

        if (!$this->validateInput($email, $accountNumber, $startDate, $endDate)) {
            http_response_code(400);
            echo "Invalid input data";
            return;
        }

        $requestId = uniqid('stmt_', true);
        
        $message = new StatementRequestMessage(
            $email,
            $accountNumber,
            $startDate,
            $endDate,
            $requestId
        );

        $this->queueService->sendMessage($message);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Запрос на генерацию выписки принят в обработку',
            'request_id' => $requestId
        ]);
    }

    private function validateInput(string $email, string $accountNumber, string $startDate, string $endDate): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (empty($accountNumber) || strlen($accountNumber) < 5) {
            return false;
        }

        if (empty($startDate) || empty($endDate)) {
            return false;
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            return false;
        }

        return true;
    }
}