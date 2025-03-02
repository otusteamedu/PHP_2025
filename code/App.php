<?php declare(strict_types=1);

namespace App;

class App {
    public function run(): string
    {
        try {
            return $this->handleRequest($_POST);
        } catch (\Exception $e) {
            // Возвращаем строку ошибки в случае исключения
            return 'Ошибка на стороне сервера: '. $e->getMessage();
        }
    }

    private function handleRequest(array $postData): string
    {
        // проверяем на ошибки на уровне запроса: что метод запроса POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            throw new \InvalidArgumentException('Разрешен только POST метод');
        }

        $handler = new RequestHandler();
        $response = $handler->processRequest($postData);

        if (isset($response['error'])) {
            throw new \InvalidArgumentException($response['error']);
        }

        return $response['success'];

    }
}