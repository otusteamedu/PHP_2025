<?php

declare(strict_types=1);

namespace App\Controller;

use App\Validator\Email\EmailValidatorInterface;

class EmailValidationController
{
    public function __construct(private EmailValidatorInterface $validator) {}

    /**
     * Считывает массивы GET и POST, валидирует, возвращает ответ‑строку
     */
    public function handle(): string
    {
        $rawInput = $this->getRawInput();

        if ($rawInput === '') {
            return $this->formatUsageHint();
        }

        $emails  = $this->parseEmails($rawInput);
        $results = $this->validator->verifyList($emails);

        return $this->formatResults($results);
    }

    /**
     * Получает параметр "emails" из массивов GET или POST
     */
    private function getRawInput(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return (string) ($_POST['emails'] ?? '');
        }

        return (string) ($_GET['emails'] ?? '');
    }

    /**
     * Преобразует строку в массив email
     */
    private function parseEmails(string $rawInput): array
    {
        $parts = explode(',', $rawInput);
        $parts = array_map('trim', $parts);

        return array_filter($parts, fn(string $item): bool => $item !== '');
    }

    /**
     * Форматирует результат в виде:
     *   email => VALID
     *   email => INVALID
     */
    private function formatResults(array $results): string
    {
        $lines = [];

        foreach ($results as $email => $ok) {
            $lines[] = $email . ' => ' . ($ok ? 'VALID' : 'INVALID');
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * Если параметр "emails" отсутствует - возвращаем сообщение, что он не передан
     */
    private function formatUsageHint(): string
    {
        return "Parameter \"emails\" not passed\n";
    }
}
