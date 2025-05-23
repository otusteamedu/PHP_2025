<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use Throwable;

final class RequestHandler
{
    public function __construct(private BracketValidator $validator, private ResponseSender $responseSender){}

    /**
     * Обрабатывает входные данные и возвращает результат в виде строки
     */
    public function handle(array $post): string
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new InvalidArgumentException('Требуется POST-запрос');
            }

            if (!array_key_exists('string', $post)) {
                throw new InvalidArgumentException('Не передан параметр string');
            }

            $this->validator->validate($post['string']);

            $this->responseSender->sendResponse(200);
            return 'OK: строка корректна';
        } catch (InvalidArgumentException $e) {
            $this->responseSender->sendResponse(400);
            return 'ERROR: ' . $e->getMessage();
        } catch (Throwable $e) {
            $this->responseSender->sendResponse(400);
            return 'ERROR: ' . $e->getMessage();
        }
    }
}
