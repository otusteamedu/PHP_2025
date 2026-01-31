<?php

namespace Pryaniki\App;

class App
{
    public function run(): void
    {
        $email = $this->getEmail();
        $isValid = EmailValidator::validate($email);
        $this->printAnswer($isValid);
    }

    private function getEmail(): string
    {
        return '';
    }

    private function printAnswer(bool $isValidEmail): void
    {
        $answer = $isValidEmail
            ? 'Email is valid'
            : 'Email is not valid';
        echo $answer;
    }
}