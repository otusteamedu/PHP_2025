<?php

namespace Pryaniki\App;

use Pryaniki\App\Domain\Validators\Fields\EmailValidator;

class App
{
    private string $email;

    public function run(): string
    {
        $this->initEmail();
        return $this->getAnswer();
    }

    private function initEmail(): void
    {
        $this->email = $this->getEmail();
    }

    private function getEmail(): string
    {
        $argv = $_SERVER['argv'];

        return $argv[1] ?? '';
    }

    private function getAnswer(): string
    {
        $emailValidator = new EmailValidator($this->email);
        $isValidEmail = $emailValidator->validate();
        $answer = 'is';

        if (!$isValidEmail) {
            $answer .= ' not';
        }

        $answerText = "Email $this->email $answer valid" . PHP_EOL;
        $answerText .= $emailValidator->getValidationError();

        return $answerText;
    }
}