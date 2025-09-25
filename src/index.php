<?php

// Управление сессиями в Redis
ini_set('session.save_handler', 'rediscluster');
ini_set('session.save_path', 'seed[]=redis-1:6379&seed[]=redis-2:6379&seed[]=redis-3:6379');
session_start();

// Демонстрация работы сессий: счетчик и имя хоста
$count = isset($_SESSION['count']) ? $_SESSION['count'] + 1 : 1;
$_SESSION['count'] = $count;
$_SESSION['hostname'] = gethostname();

echo 'Hostname: '.$_SESSION['hostname'].'<br>';
echo 'Request count: '.$_SESSION['count'].'<br><br>';

set_exception_handler(function (Throwable $e) {
    http_response_code($e->getCode() ?: 400);
    echo 'Ошибка: '.$e->getMessage();
});

/**
 * Класс для валидации строки со скобками.
 */
class BracketValidator
{
    /**
     * Проверяет, является ли строка со скобками корректной.
     */
    public function isValid(string $string): bool
    {
        $stack = [];
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $char = $string[$i];
            if ($char === '(') {
                array_push($stack, $char);
            } elseif ($char === ')') {
                if (empty($stack)) {
                    return false;
                }
                array_pop($stack);
            }
        }

        return empty($stack);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Метод не поддерживается. Используйте POST.';
    exit;
}

if (! isset($_POST['string']) || $_POST['string'] === '') {
    throw new InvalidArgumentException("Параметр 'string' отсутствует или пуст.", 400);
}

$inputString = $_POST['string'];

$validator = new BracketValidator;

if ($validator->isValid($inputString)) {
    http_response_code(200);
    echo 'Строка корректна.';
} else {
    throw new RuntimeException('Строка некорректна.', 400);
}
