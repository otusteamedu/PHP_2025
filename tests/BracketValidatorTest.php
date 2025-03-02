<?php

require __DIR__ . '/../vendor/autoload.php';

use App\BracketValidator;

class BracketValidatorTest
{
    public static function runTests()
    {
        $positiveTests = [
            "(())()",
            "((()))",
            "()()()",
            "(((((())))))",
        ];

        $negativeTests = [
            "(()()/)",
            "(()))",
            "((())",
            "())(",
            ")(",
            "(abc)",
            "",
        ];

        foreach ($positiveTests as $input) {
            $validator = new BracketValidator($input);
            $result = $validator->isValid();
            $status = array_key_exists('success', $result) ? "✅ Passed" : "❌ Failed";
            echo "Строка: \"{$input}\". Статус: {$status}". PHP_EOL;
        };

        foreach ($negativeTests as $input) {
            $validator = new BracketValidator($input);
            $result = $validator->isValid();  
            $status = array_key_exists('error', $result) ? "✅ Passed" : "❌ Failed";
            echo "Строка: \"{$input}\". Статус: {$status}". PHP_EOL;
        };

    }
}

// Запуск тестов
BracketValidatorTest::runTests();