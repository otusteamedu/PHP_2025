<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\CreateStatementRequestDTO;
use App\Application\UseCase\CreateStatementRequestUseCase;
use App\Application\UseCase\ProcessStatementRequestUseCase;
use Exception;

final class StatementRequestController
{
    public function __construct(
        private CreateStatementRequestUseCase $createUseCase,
        private ProcessStatementRequestUseCase $processUseCase
    ) {
    }

    /**
     * Создает новый запрос на выписку
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed'], JSON_THROW_ON_ERROR);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

            if (!$input) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON'], JSON_THROW_ON_ERROR);
                return;
            }

            if (empty($input['startDate']) || empty($input['endDate']) || empty($input['telegramChatId'])) {
                http_response_code(400);
                echo json_encode(
                    ['error' => 'Missing required fields: startDate, endDate, telegramChatId'],
                    JSON_THROW_ON_ERROR
                );
                return;
            }

            $dto = new CreateStatementRequestDTO(
                startDate: $input['startDate'],
                endDate: $input['endDate'],
                telegramChatId: $input['telegramChatId']
            );

            $result = $this->createUseCase->execute($dto);

            echo json_encode($result->toArray(), JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    /**
     * Отображает форму для создания запроса
     */
    public function showForm(): void
    {
        include __DIR__ . '/../View/statement-form-telegram.php';
    }
}
