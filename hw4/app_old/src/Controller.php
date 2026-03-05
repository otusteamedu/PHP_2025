<?php


class Controller {
    public function handleRequest() {

        $rawString = $_POST['string'] ?? '';

        if($rawString === '') {
            $this->sendResponse(400, "всё плохо, давай заново!");
            return;
        }
        // Удаляем лишние кавычки, если они приходят внутри строки
        $prepareString = trim($rawString, '"');

        if($this->isValidBrackets($prepareString)) {
            $this->sendResponse(200, "Все ок");
        } else {
            $this->sendResponse(400, "всё плохо, давай заново!");
        }

    }

    private function sendResponse(int $code, string $message) {
        http_response_code($code);
        echo $message;
        // exit;
    }

    private function isValidBrackets(string $str): bool {
        $balance = 0;
        $length = strlen(trim($str, '"'));

        for ($i = 0; $i < $length; $i++) {
            $char = $str[$i];
            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
            }
            // Если закрывающих больше, чем открытых в любой момент времени
            if ($balance < 0) return false;
        }
        return $balance === 0;
    }

}