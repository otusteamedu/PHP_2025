<?php

class CheckParentheses {
    /**
     * @throws Exception
     */
    public function check(string $postString): string
    {
        if (strlen($postString) === 0) {
            http_response_code(400);
            throw new Exception("Пустая строка");
        }

        if ($this->containsOnlyParentheses($postString) === false) {
            http_response_code(400);
            throw new Exception("Нужна строка только с круглыми скобками");
        }

        if (!$this->checkParenthesesBalance($postString)) {
            http_response_code(400);
            throw new Exception("Скобки не сбалансированы");
        }

        http_response_code(200);
        return 'Скобки сбалансированы';
    }

    private function containsOnlyParentheses(string $postString): bool {
        return preg_match('/^[()]+$/', $postString) === 1;
    }

    private function checkParenthesesBalance(string $postString): bool {
        $balance = 0;

        for ($i = 0; $i < strlen($postString); $i++) {
            $char = $postString[$i];

            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
                if ($balance < 0) {
                    return false;
                }
            }
        }

        return $balance === 0;
    }
}

try {
    $checkParentheses = new CheckParentheses();
    echo $checkParentheses->check($_POST['string'] ?? '');
} catch (Exception $ex) {
    echo $ex->getMessage();
}
