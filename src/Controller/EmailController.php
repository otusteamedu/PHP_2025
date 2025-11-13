<?php
namespace Aovchinnikova\Hw15\Controller;

use Aovchinnikova\Hw15\Service\EmailValidationService;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class EmailController
{
    private EmailValidationService $emailValidationService;
    private AMQPStreamConnection $rabbitConnection;

    public function __construct()
    {
        $this->emailValidationService = new EmailValidationService();
        $this->rabbitConnection = $this->createRabbitConnection();
    }

    public function handleValidationRequest(): void
    {
        try {
            $requestPayload = file_get_contents('php://input');
            $parsedInput = json_decode($requestPayload, true);

            if ($parsedInput === null || !isset($parsedInput['emails'])) {
                throw new Exception('Invalid JSON or missing "emails" field.');
            }

            $results = $this->validateEmails($parsedInput['emails']);

            $this->sendToQueue($requestPayload);

            header('Content-Type: application/json');
            echo json_encode($results);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function createRabbitConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'] ?? 'rabbitmq',
            $_ENV['RABBITMQ_PORT'] ?? 5672,
            $_ENV['RABBITMQ_USER'] ?? 'guest',
            $_ENV['RABBITMQ_PASS'] ?? 'guest'
        );
    }

    private function validateEmails(array $emails): array
    {
        return array_map(
            fn($email) => $this->emailValidationService->validate($email)->toArray(),
            array_combine($emails, $emails)
        );
    }

    private function sendToQueue(string $message): void
    {
        $channel = $this->rabbitConnection->channel();
        $channel->queue_declare('email_validation', false, true, false, false);
        
        $msg = new \PhpAmqpLib\Message\AMQPMessage(
            $message,
            ['delivery_mode' => \PhpAmqpLib\Message\AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );
        
        $channel->basic_publish($msg, '', 'email_validation');
        $channel->close();
    }

    public function __destruct()
    {
        $this->rabbitConnection->close();
    }
}
