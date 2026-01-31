<?php

namespace Pryaniki\App;

class App
{
    private string $email;

    public function run(): void
    {
        $this->initEmail();
        $emailValidator = new EmailValidator($this->email);
        $isValid = $emailValidator->validate();
        $this->printAnswer($isValid);
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

    private function printAnswer(bool $isValidEmail): void
    {
        $answer = 'is';

        if (!$isValidEmail) {
            $answer .= ' not';
        }

        echo "Email $this->email $answer valid" . PHP_EOL;
    }
}