<?php

declare(strict_types=1);

namespace App;

use App\Validator\ParenthesisValidator;

class Application
{
    public function run(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $parenthesisValidate = new ParenthesisValidator($_POST['string'] ?? '');
                $parenthesisValidate->isValidate();
            } else {
                throw new \Exception('Не правильный метод проверки', 400);
            }
        } catch (\Exception $e) {
            http_response_code($e->getCode());

            echo $e->getMessage();
        }
    }
}
