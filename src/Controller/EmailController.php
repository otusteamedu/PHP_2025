<?php
namespace Aovchinnikova\Hw15\Controller;

use Aovchinnikova\Hw15\Service\EmailValidationService;
use Exception;

class EmailController
{
    private $emailValidationService;

    // Нарушение: Жесткое создание зависимости (нарушение D из SOLID). Добавить объекты.
    // Решение: Внедрить зависимость через конструктор.
    // Правильный код:
    // private EmailValidationService $emailValidationService;
    // private RequestParser $requestParser;
    // private ErrorHandler $errorHandler;
    // public function __construct(EmailValidationService $emailValidationService, RequestParser $requestParser, private ErrorHandler $errorHandler) {
    //     $this->emailValidationService = $emailValidationService;
    //     $this->requestParser = $requestParser;
    //     $this->errorHandler = $errorHandler;
    // }
    public function __construct()
    {
        $this->emailValidationService = new EmailValidationService();
    }

    /**
     * Нарушение: Метод делает слишком много (нарушение Single Responsibility).
     * Решение: Вынести парсинг запроса и формирование ответа в отдельные классы.
     */
    public function handleValidationRequest()
    {
        try {
            // Нарушение: Парсинг JSON должен быть в отдельном классе (например, RequestParser).
            // Правильный код:
            // $input = $this->requestParser->getJsonInput();
            $requestPayload = file_get_contents('php://input');
            $parsedInput = json_decode($requestPayload, true);

            if ($parsedInput === null || !isset($parsedInput['emails'])) {
                throw new Exception('Invalid JSON or missing "emails" field.');
            }

            $results = $this->validateEmails($parsedInput['emails']);

            // Нарушение: Форматирование ответа должно быть в отдельном классе (например, JsonResponseFormatter).
            // Правильный код:
            // JsonResponseFormatter::send($results);
            header('Content-Type: application/json');
            
            // Нарушение: echo - это побочный эффект у метода. Нужно применять return или yield
            // Правильный код:
            // return $this->responseFormatter->formatResponse($results);
            echo json_encode($results);

        } catch (Exception $e) {
            // Нарушение: Обработка ошибок должна быть централизованной.
            // Правильный код:
            // $this->errorHandler->handle($e);
            http_response_code(400);
            
            // Нарушение: echo - это побочный эффект у метода. Нужно генерировать исключение
            // Правильный код:
            // throw $e;
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Нарушение: Метод приватный, но может быть полезен для тестирования.
    // Решение: Сделать protected или вынести в сервис.
    private function validateEmails(array $emails): array
    {
        $results = [];
        foreach ($emails as $email) {
            $results[$email] = $this->emailValidationService->validate($email);
        }
        return $results;
    }
}
