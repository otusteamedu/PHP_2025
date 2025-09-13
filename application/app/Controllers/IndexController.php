<?php

namespace App\Controllers;

use App\Base\Views\ViewManager;
use App\Services\EmailService;

class IndexController
{
    public function __construct(
        protected ViewManager $viewManager,
        protected EmailService $emailService,
    ) {
    }

    public function index(): void
    {
        $string = $_POST['emails'] ?? '';
        $validEmails = $this->emailService->parseEmails($string);
        $this->viewManager->renderTemplate('index.html', compact('string', 'validEmails'));
    }
}