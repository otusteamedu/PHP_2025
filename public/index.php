<?php

use App\Exceptions\EmptyPostException;
use App\Exceptions\UnbalancedStringException;
use App\Validation\ParenthesesBalance;

require __DIR__ . '/../vendor/autoload.php';

try {
    $string = $_POST["string"] ?? null;

    if (empty($string)) {
        throw new EmptyPostException('Строка для проверки отсутствует в запросе.');
    }

    $result = new ParenthesesBalance()->validate($string);

    if ($result === false) {
        throw new UnbalancedStringException(
            "Строка '$string' не сбалансирована по открывающим/закрывающим скобкам."
        );
    }
} catch (EmptyPostException|UnbalancedStringException $exception) {
    http_response_code(400);
    echo $exception->getMessage() . PHP_EOL;
    return;
}

echo "Строка '$string' сбалансирована по открывающим/закрывающим скобкам.";
