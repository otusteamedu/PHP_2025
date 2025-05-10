<?php

declare(strict_types=1);

namespace App;

use App\Validator\ParenthesisValidator;
use App\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    public function run(): void
    {
        $view = new View();
        $request = Request::createFromGlobals();
        $isMethodPost = $request->isMethod(Request::METHOD_POST);

        try {
            $dataTemplate = [
                'alert' => 'Используйте форму для проверки или curl с методом POST',
                'isMethodPost' => $isMethodPost,
            ];
            $httpCode = Response::HTTP_OK;

            if ($isMethodPost) {
                $stringInput = $request->request->get('string', '');
                $parenthesisValidate = new ParenthesisValidator($stringInput);

                $parenthesisValidate->isValidate() ?
                    throw new \Exception('Всё хорошо', Response::HTTP_OK) :
                    throw new \Exception('Всё плохо', Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            $httpCode = $e->getCode();

            $dataTemplate = [
                'alert' => $e->getMessage(),
                'isMethodPost' => $isMethodPost,
            ];
        }

        $content = $view->render('form', $dataTemplate);

        $response = new Response($content, $httpCode);
        $response->send();
    }
}
