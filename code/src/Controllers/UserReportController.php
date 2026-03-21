<?php

declare(strict_types=1);

namespace Ak\Hw\Controllers;

use Ak\Hw\Infrastructure\Messaging\RabbitMqConsumer;
use Ak\Hw\Infrastructure\Messaging\RabbitMqProducer;
use Ak\Hw\Models\UserReportMessageHandler;
use Ak\Hw\Validation\Email as EmailValidator;
use Ak\Hw\Validation\DateValidator;
use Ak\Hw\Services\UserReportService;


class UserReportController
{
    public function handleReportRequest(): void
    {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $dateFrom = filter_input(INPUT_POST, 'date_from');
        $dateTo = filter_input(INPUT_POST, 'date_to');

        $errors = [];

        // Validate data
        if ($userId === false) {
            $errors['user_id'] = 'Invalid user ID.';
        }

        $emailValidator = new EmailValidator();
        if (!$email || !$emailValidator->isValid($email)) {
            $errors['email'] = 'Invalid email address.';
        }

        $dateValidator = new DateValidator();
        if (!$dateFrom || !$dateValidator->isValid($dateFrom)) {
            $errors['date_from'] = 'Invalid "from" date.';
        }

        if (!$dateTo || !$dateValidator->isValid($dateTo)) {
            $errors['date_to'] = 'Invalid "to" date.';
        }

        if (!empty($errors)) {
            http_response_code(400); // Bad Request
            echo json_encode(['errors' => $errors]);
            exit;
        }



        $rabbitMqProducer = null;
        try {
            $rabbitMqProducer = new RabbitMqProducer($_ENV['RABBITMQ_HOST'], (int)$_ENV['RABBITMQ_PORT'], $_ENV['RABBITMQ_LOGIN'], $_ENV['RABBITMQ_PASSWORD']);
            $userReportService = new UserReportService($rabbitMqProducer);
            $reportData = $userReportService->generateReport($userId, $email, $dateFrom, $dateTo);

            $response = [
                'status' => 'success',
                'data' => $reportData
            ];


        } catch (\Exception $e) {
            http_response_code(500); // Internal Server Error
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        } finally {
            if ($rabbitMqProducer) {
                $rabbitMqProducer->close();
            }
        }

        // 4. Response
        echo json_encode($response);
    }

    public function queueHandler(): void
    {
        $consumer = new RabbitMqConsumer($_ENV['RABBITMQ_HOST'], (int)$_ENV['RABBITMQ_PORT'], $_ENV['RABBITMQ_LOGIN'], $_ENV['RABBITMQ_PASSWORD']);
        $handler = new UserReportMessageHandler();
        $queueName = 'report generation';
        $consumer->consume($queueName, $handler);
        $consumer->close();
    }

}
