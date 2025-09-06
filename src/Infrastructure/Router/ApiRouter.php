<?php

declare(strict_types=1);

namespace App\Infrastructure\Router;

use App\Application\DTO\CreateRequestDTO;
use App\Application\UseCase\CreateRequestUseCase;
use App\Application\UseCase\GetRequestStatusUseCase;
use App\Infrastructure\Logging\Logger;
use Exception;
use InvalidArgumentException;
use RuntimeException;

final class ApiRouter
{
    private \Monolog\Logger $logger;

    public function __construct(
        private CreateRequestUseCase $createRequestUseCase,
        private GetRequestStatusUseCase $getRequestStatusUseCase
    ) {
        $this->logger = Logger::getInstance();
    }

    /**
     * Обрабатывает входящие HTTP запросы и маршрутизирует их
     */
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $this->logger->info('Request received', [
            'method' => $method,
            'uri' => $uri,
        ]);

        // Health check
        if ($uri === '/api/health') {
            $this->handleHealthCheck();
            return;
        }

        // API routes
        if ($uri === '/api/requests' && $method === 'POST') {
            $this->handleCreateRequest();
            return;
        }

        if (preg_match('/^\/api\/requests\/([^\/]+)$/', $uri, $matches) && $method === 'GET') {
            $this->handleGetRequestStatus($matches[1]);
            return;
        }

        // Swagger documentation
        if ($uri === '/api/docs') {
            $this->handleApiDocs();
            return;
        }

        $this->sendErrorResponse('Not Found', 404);
    }

    /**
     * Обрабатывает запрос health check API
     */
    private function handleHealthCheck(): void
    {
        $timestamp = (new \DateTimeImmutable())->format('c');
        $this->sendJsonResponse([
            'status' => 'ok',
            'timestamp' => $timestamp,
        ]);
    }

    /**
     * Обрабатывает создание нового запроса
     */
    private function handleCreateRequest(): void
    {
        $input = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

        if (!$input) {
            $this->sendErrorResponse('Invalid JSON', 400);
            return;
        }

        try {
            $dto = CreateRequestDTO::fromArray($input);
            $response = $this->createRequestUseCase->execute($dto);

            $this->sendJsonResponse($response->toArray(), 201);
        } catch (Exception $e) {
            $this->logger->error('Error creating request', [
                'error' => $e->getMessage(),
                'input' => $input,
            ]);

            $this->sendErrorResponse('Bad Request', 400);
        }
    }

    /**
     * Обрабатывает получение статуса запроса по ID
     */
    private function handleGetRequestStatus(string $id): void
    {
        try {
            $response = $this->getRequestStatusUseCase->execute($id);
            $this->sendJsonResponse($response->toArray());
        } catch (InvalidArgumentException $e) {
            $this->sendErrorResponse('Not Found', 404);
        } catch (Exception $e) {
            $this->logger->error('Error getting request status', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            $this->sendErrorResponse('Internal Server Error', 500);
        }
    }


    /**
     * Обрабатывает запрос на отображение Swagger UI документации
     */
    private function handleApiDocs(): void
    {
        try {
            $yamlFile = __DIR__ . '/../../../docs/swagger.yaml';

            if (!file_exists($yamlFile)) {
                $this->logger->error('Swagger YAML file not found', ['file' => $yamlFile]);
                throw new RuntimeException('Swagger documentation file not found');
            }

            $yamlContent = file_get_contents($yamlFile);
            if ($yamlContent === false) {
                $this->logger->error('Failed to read Swagger YAML file', ['file' => $yamlFile]);
                throw new RuntimeException('Failed to read Swagger documentation file');
            }

            $spec = yaml_parse($yamlContent);
            if ($spec === false) {
                $this->logger->error('Failed to parse Swagger YAML file', ['file' => $yamlFile]);
                throw new RuntimeException('Failed to parse Swagger documentation file');
            }

            $specJson = json_encode($spec, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

            // загружаем HTML шаблон
            $templateFile = __DIR__ . '/../../../templates/swagger-ui.html';
            if (!file_exists($templateFile)) {
                $this->logger->error('Swagger template file not found', ['file' => $templateFile]);
                throw new RuntimeException('Swagger template file not found');
            }

            $html = file_get_contents($templateFile);
            if ($html === false) {
                $this->logger->error('Failed to read Swagger template file', ['file' => $templateFile]);
                throw new RuntimeException('Failed to read Swagger template file');
            }

            // заменяем плейсхолдер на JSON спецификацию
            $html = str_replace('{{SPEC_JSON}}', $specJson, $html);

            header('Content-Type: text/html');
            echo $html;
        } catch (Exception $e) {
            $this->logger->error('Error loading API documentation', [
                'error' => $e->getMessage(),
            ]);

            $this->sendErrorResponse('Failed to load API documentation', 500);
        }
    }


    /**
     * Отправляет JSON ответ
     */
    private function sendJsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * Отправляет JSON ответ с ошибкой
     */
    private function sendErrorResponse(string $message, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $message,
            'status' => $statusCode,
        ], JSON_THROW_ON_ERROR);
    }
}
