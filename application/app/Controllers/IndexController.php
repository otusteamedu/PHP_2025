<?php

namespace App\Controllers;

use App\Base\Views\ViewManager;
use App\Services\ValidateBracketService;

class IndexController
{
    public function __construct(
        protected ValidateBracketService $bracketService,
        protected ViewManager $viewManager,
    ) {
    }

    public function index(): void
    {
        $string = $_POST['string'] ?? '';
        $message = '';
        if (!$this->bracketService->validate($string)) {
            http_response_code(400);
            $message = 'Скобки не валидны.';
        }
        $this->viewManager->renderTemplate('index.html', compact('string', 'message'));
    }
}