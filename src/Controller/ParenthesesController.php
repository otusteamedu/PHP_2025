<?php

namespace Aovchinnikova\Hw14\Controller;

use Aovchinnikova\Hw14\Model\ParenthesesValidator;
use Aovchinnikova\Hw14\View\ResultView;

class ParenthesesController
{
    public function handleRequest()
    {
        try {
            $inputString = $_POST['string'] ?? '';

            if (empty($inputString)) {
                throw new \Exception("Запрос пустой. Пожалуйста, предоставьте строку.");
            }

            $validator = new ParenthesesValidator();
            $isValid = $validator->isValid($inputString);

            $statusCode = $isValid ? 200 : 400;
            $message = $isValid ? "Все хорошо. Строка корректна." : "Ошибка. Некорректное количество открытых и закрытых скобок.";

        } catch (\Exception $e) {
            $statusCode = 400;
            $message = $e->getMessage();
        }

        $view = new ResultView();
        $view->render($statusCode, $message);
    }
}