<?php

declare(strict_types=1);

namespace App;

use App\Service\EmailVerificationService;
use App\View\View;

class Application
{
    public function run(): void
    {
        $view = new View();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emails = \explode(";", trim($_POST['emails']));
            $emailVerificationService = new EmailVerificationService();
            $resEmails = $emailVerificationService($emails);

            $dataTemplate = [
                'alert' => '',
                'resEmails' => $resEmails
            ];
        } else {
            $dataTemplate = [
                'alert' => 'Используйте форму или curl запрос с методом POST',
                'resEmails' => [],
            ];
        }

        echo $view->render('form', $dataTemplate);
    }
}
