<?php

namespace classes;

class App
{
    public function run()
    {
        $emailValidator = new EmailValidator();

        $arEmails = [
            'pavel.klimenko.1989@gmail.com',
            'pavel89@rambler.ru',
            'pavel.klimenko.89@yandex.by',
            'testMail',
            'testMail@eeeeeee.su',
        ];

        return $emailValidator->validateEmailList($arEmails);
    }

}