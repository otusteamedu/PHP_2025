<?php

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\EmailValidatorService;

class EmailValidatorController implements Controller
{

    public function __invoke(Request $request): \App\Http\Response
    {
        $body = $request->getArrayBody();
        $formData = $request->getPostData();
        $emails = null;
        if (isset($body['emails']))
        {
            $emails = $body['emails'];

        } elseif(isset($formData['emails']))
        {
            $emails = $formData['emails'];
        }

        if (!isset($emails))
        {
            throw new \App\Exception\HttpException('Параметр "emails" является обязательным');
        }

        $valid = EmailValidatorService::validate($emails);

        return (new Response(['message' => $valid ? 'Переданный список email валиден' : 'Переданный список email не валиден'], 200));
    }
}