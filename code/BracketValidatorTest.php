<?php

require_once 'BracketValidator.php';

class BracketValidatorTest
{
    /**
     * Запускает тесты для проверки работы BracketValidator
     */
    public static function runTests()
    {
        $tests = [
            "(()()/)" => false,
            "(())()" => true,
            "(()))" => false,
            "((()))" => true,
            "()()()" => true,
            "((())" => false,
            "())(" => false,
            "" => false,
            "(((((())))))" => true,
            ")(" => false,
            "(abc)" => false,
            
        ];

        foreach ($tests as $input => $expected) {
            $validator = new BracketValidator($input);
            $result = $validator->isValid();
            
            echo "<pre>";
            echo "Тестовая строка: \"{$input}\"" . PHP_EOL;
            echo "Ожидалось: " . ($expected ? "true" : "false") . PHP_EOL;
            echo "Получено: " . ($result ? "true" : "false") . PHP_EOL;
            echo "----------------------------" . PHP_EOL;
            echo "</pre>";
        }
    }
}

// Запуск тестов
BracketValidatorTest::runTests();