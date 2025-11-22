<?php

namespace Alisaselezneva\Code\Infrastructure\Http;

use Alisaselezneva\Code\Domain\Services\StringProcessor;

class Request
{
    private StringProcessor $processor;

    public function __construct()
    {
        $this->processor = new StringProcessor();
    }

    public function handle(): void
    {
        $input = $_POST['string'] ?? '';

        try {
            $this->processor->validate($input);
            
            http_response_code(200);
            echo json_encode([
                'container' => $_SERVER['HOSTNAME'],
                'message' => 'Строка корректна'
            ]);

        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode([
                'container' => $_SERVER['HOSTNAME'],
                'error' => 'Строка не корректна',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'container' => $_SERVER['HOSTNAME'],
                'error' => 'Внутренняя ошибка',
                'message' => $e->getMessage()
            ]);
        }
    }
}