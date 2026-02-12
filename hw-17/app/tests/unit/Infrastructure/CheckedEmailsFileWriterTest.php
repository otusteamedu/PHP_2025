<?php

declare(strict_types=1);

namespace unit\Infrastructure;

use App\Infrastructure\CheckedEmailsFileWriter;
use Codeception\Test\Unit;

final class CheckedEmailsFileWriterTest extends Unit
{
    private string $testFilePath;

    protected function setUp(): void
    {
        $this->testFilePath = tempnam(sys_get_temp_dir(), 'emails_test_');
    }

    public function testWriteEmails(): void
    {
        $emails = ['test1@example.com', 'test2@example.com'];

        $writer = new CheckedEmailsFileWriter($this->testFilePath);
        $writer->writeEmails($emails);

        $content = file_get_contents($this->testFilePath);

        $this->assertStringContainsString("test1@example.com\n", $content);
        $this->assertStringContainsString("test2@example.com\n", $content);

        $this->assertStringEndsWith(PHP_EOL, $content);
    }

    protected function tearDown(): void
    {
        if (is_file($this->testFilePath)) {
            unlink($this->testFilePath);
        }
    }
}
