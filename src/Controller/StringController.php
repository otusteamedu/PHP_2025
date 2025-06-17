<?php

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

class StringController implements Controller
{

    public function __invoke(Request $request): \App\Http\Response
    {
        $body = $request->getArrayBody();
        $formData = $request->getPostData();
        $string = null;
        if (isset($body['string']))
        {
            $string = $body['string'];

        } elseif(isset($formData['string']))
        {
            $string = $formData['string'];
        }

        if ($string === null || $string === '')
        {
            throw new \App\Exception\HttpException('Параметр string является обязательным');
        }

        if (!\App\StringValidator::isStaplesValid($string)) {
            throw new \App\Exception\ValidateException('Строка некорретная. Присутствуют лишние скобочки');
        } else {
            $message = 'Строка корректна';
        }

        return (new Response(['message' => $message], 200));
    }
}