<?php
declare(strict_types=1);

namespace Unit\Service;

use Dinargab\Homework5\Result\ValidationResult;
use Dinargab\Homework5\Service\EmailResultFormatter;
use PHPUnit\Framework\TestCase;

class EmailResultFormatterTest extends TestCase
{

    private EmailResultFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new EmailResultFormatter();
    }

    public function testFormatWithValidAndInvalidResults(): void
    {
        $results = [
            new ValidationResult('valid@example.com', true),
            new ValidationResult('invalid@nomxdomain.com', false, 'No MX records found for email domain'),
            new ValidationResult('another@example.com', true)
        ];

        $formatted = $this->formatter->format($results);

        $this->assertStringContainsString('valid@example.com is valid<br>', $formatted);
        $this->assertStringContainsString('invalid@nomxdomain.com is invalid, Error message:No MX records found for email domain<br>', $formatted);
        $this->assertStringContainsString('another@example.com is valid<br>', $formatted);
    }

    public function testFormatWithEmptyArray(): void
    {
        $formatted = $this->formatter->format([]);

        $this->assertEquals('', $formatted);
    }

    public function testFormatWithOnlyValidResults(): void
    {
        $results = [
            new ValidationResult('test1@example.com', true),
            new ValidationResult('test2@example.com', true)
        ];

        $formatted = $this->formatter->format($results);

        $this->assertStringContainsString('test1@example.com is valid<br>', $formatted);
        $this->assertStringContainsString('test2@example.com is valid<br>', $formatted);
        $this->assertStringNotContainsString('Error message', $formatted);
    }

    public function testFormatWithOnlyInvalidResults(): void
    {
        $results = [
            new ValidationResult('invalid1@example.com', false, 'Error 1'),
            new ValidationResult('invalid2@example.com', false, 'Error 2')
        ];

        $formatted = $this->formatter->format($results);

        $this->assertStringContainsString('invalid1@example.com is invalid, Error message:Error 1<br>', $formatted);
        $this->assertStringContainsString('invalid2@example.com is invalid, Error message:Error 2<br>', $formatted);
    }

}
