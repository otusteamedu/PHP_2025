<?php

declare(strict_types=1);

namespace App;

use App\Service\EmailVerificationService;
use App\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    public function run(): void
    {
        $view = new View();
        $request = Request::createFromGlobals();

        if ($request->isMethod(Request::METHOD_POST)) {
            $emailsInput =$request->request->get('emails', '');
            $emails = \explode(';', trim($emailsInput));
            $emailsFiltered = \array_filter($emails, fn ($email) => !empty($email));

            $emailVerificationService = new EmailVerificationService();
            $resEmails = $emailVerificationService($emailsFiltered);

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

        $content = $view->render('form', $dataTemplate);

        $response = new Response($content);
        $response->send();
    }
}
