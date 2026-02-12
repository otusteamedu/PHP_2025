<?php

declare(strict_types=1);

namespace unit\Infrastructure;

use App\Infrastructure\EmailsFileReader;
use Codeception\Test\Unit;
use RuntimeException;

final class EmailsFileReaderTest extends Unit
{
    private string $testFilePath;

    protected function setUp(): void
    {
        $this->testFilePath = tempnam(sys_get_temp_dir(), 'emails_reader_test_');
    }

    public function testReadEmails(): void
    {
        file_put_contents($this->testFilePath, "a@example.com\nb@example.com\n");

        $reader = new EmailsFileReader($this->testFilePath);
        $result = $reader->readEmails();

        $this->assertSame(['a@example.com', 'b@example.com'], $result);
    }

    public function testReadEmailsThrowsException(): void
    {
        $nonExistentPath = sys_get_temp_dir() . '/no_such_file_' . uniqid();

        $reader = new EmailsFileReader($nonExistentPath);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File not found");

        $reader->readEmails();
    }

    protected function tearDown(): void
    {
        if (is_file($this->testFilePath)) {
            unlink($this->testFilePath);
        }
    }
}
