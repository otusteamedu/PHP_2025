<?php
declare(strict_types=1);

namespace Integration;

use Dinargab\Homework5\Result\ValidationResult;
use Dinargab\Homework5\Service\EmailValidator;
use Dinargab\Homework5\Service\FormatterInterface;
use PHPUnit\Framework\TestCase;

class EmailValidationIntegrationTest extends TestCase
{
    private EmailValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new EmailValidator();
    }

    public function testFullValidationAndFormattingFlow(): void
    {
        // Подготовка данных
        $emails = [
            'test@example.com',
            'invalid-email',
            'another@test.com'
        ];

        $validationResults = $this->validator->verifyEmails($emails);

        // Проверка результатов валидации
        $this->assertCount(3, $validationResults);
        $this->assertInstanceOf(ValidationResult::class, $validationResults[0]);

        // Шаг 2: Форматирование результатов
        $formattedOutput = $this->validator->verifyEmails($emails, true);

        // Проверка форматированного вывода
        $this->assertStringContainsString('test@example.com', $formattedOutput);
        $this->assertStringContainsString('invalid-email', $formattedOutput);
        $this->assertStringContainsString('another@test.com', $formattedOutput);
        $this->assertStringContainsString('is valid', $formattedOutput);
        $this->assertStringContainsString('is invalid', $formattedOutput);
        $this->assertStringContainsString('<br>', $formattedOutput);
        $this->assertStringContainsString('test@example.com is valid', $formattedOutput);
        $this->assertStringContainsString('invalid-email is invalid', $formattedOutput);
    }

    public function testFormatterSwapIntegration(): void
    {
        // Создаем кастомный форматтер
        $customFormatter = new class implements FormatterInterface {
            public function format(array $returnArray): string
            {
                $output = [];
                foreach ($returnArray as $result) {
                    $status = $result->isValid() ? '+' : '-';
                    $output[] = $status . ' ' . $result->getInputValue();
                }
                return implode(PHP_EOL, $output);
            }
        };

        $emails = ['test@example.com', 'invalidemail'];


        $this->validator->setFormatter($customFormatter);

        $result = $this->validator->verifyEmails($emails, true);
        $this->assertStringContainsString('+', $result);
        $this->assertStringContainsString('-', $result);
        foreach ($emails as $email) {
            $this->assertStringContainsString($email, $result);
        }
        $this->assertStringContainsString(PHP_EOL, $result);
        $this->assertStringNotContainsString('<br>', $result);
    }
}