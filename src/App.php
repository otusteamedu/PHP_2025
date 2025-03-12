<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\ValidationException;

require __DIR__ . '/CustomSessionHandler.php';
require __DIR__ . '/Validator.php';
require __DIR__ . '/ViewsHandler.php';

class App
{
    private CustomSessionHandler $customSessionHandler;
    private Validator $validator;
    private ViewsHandler $viewsHandler;

    public function __construct()
    {
        $this->customSessionHandler = new CustomSessionHandler();
        $this->validator = new Validator();
        $this->viewsHandler = new ViewsHandler();
    }

    public function run(): string
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            return $this->handleGet();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return $this->handlePost();
        }

        return '';
    }

    private function handleGet(): string
    {
        $this->customSessionHandler->startSession();

        $views = $this->viewsHandler->getViewsCount();

        $output = "Количество просмотров: " . $views;

        $this->customSessionHandler->closeSession();

        return $output;
    }

    private function handlePost(): string
    {
        $string = $_POST['string'];
        try {
            $this->validator->validateStringLength($string);
            $this->validator->validateParentheses($string);
        } catch (ValidationException $e) {
            http_response_code(400);
            return $e->getMessage();
        }

        http_response_code(200);

        return "String is valid!";
    }
}