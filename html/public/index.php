<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Otus\Strategy\CounterStrategy;
use Otus\Strategy\ReplaceStrategy;
use Otus\Strategy\ValidatorContext;

session_start();

if (empty($_SESSION['datetime'])) {
    $_SESSION['datetime'] = new DateTime()->format('Y-m-d');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validate = false;
    $string = (string) $_POST['string'] ?? '';

    $string = preg_replace('/[^\(\)]+/', '', $string);

    if (!empty($string)) {
        $strategies = [
            new CounterStrategy(),
            new ReplaceStrategy(),
        ];

        shuffle($strategies);

        $strategy = array_pop($strategies);

        $validate = new ValidatorContext($strategy)->validate($string);
    }

    if ($validate) {
        header('HTTP/1.1 200 OK');

        echo 'OK : ' . gethostname();
    } else {
        header('HTTP/1.1 400 Bad Request');

        echo 'Bad Request : ' . gethostname();
    }
} else {
    echo 'Container : ' . gethostname() . ' ; SESSION : ' . $_SESSION['datetime'];
}
