<?php

declare(strict_types=1);

namespace App;

use App\Validator\ParenthesisValidator;
use App\View\View;

class Application
{
    public function run(): void
    {
        $view = new View();
        $isMethodPost = $_SERVER['REQUEST_METHOD'] === 'POST';

        try {
            $dateTemplate = [
                'alert' => 'Используйте форму для проверки или curl с методом POST',
                'isMethodPost' => $isMethodPost,
            ];

            if ($isMethodPost) {
                $parenthesisValidate = new ParenthesisValidator($_POST['string'] ?? '');
                $parenthesisValidate->isValidate() ?
                    throw new \Exception('Всё хорошо', 200) :
                    throw new \Exception('Всё плохо', 400);
            }
        } catch (\Exception $e) {
            http_response_code($e->getCode());

            $dateTemplate = [
                'alert' => $e->getMessage(),
                'isMethodPost' => $isMethodPost,
            ];
        }

        echo $view->render('form', $dateTemplate);
    }
}
