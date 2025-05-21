<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use Throwable;

final class RequestHandler
{
    public function __construct(private BracketValidator $validator){}

    /**
     * Читает POST-параметр `string`, валидирует и отправляет HTTP‑ответ
     */
    public function handle(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new InvalidArgumentException('Требуется POST-запрос');
            }

            $post = $_POST;

            if (!array_key_exists('string', $post)) {
                throw new InvalidArgumentException('Не передан параметр string');
            }

            $this->validator->validate($post['string']);

            http_response_code(200);
            echo 'OK: строка корректна';
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo 'ERROR: ' . $e->getMessage();
        } catch (Throwable $e) {
            http_response_code(400);
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}

