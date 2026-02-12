<?php
namespace Tests\Integration;

use App\Service\EmailProcess;
use PHPUnit\Framework\TestCase;

/**
 * Класс EmailProcessIntegrationTest
 * 
 * Интеграционные тесты: взаимодействие с файловой системой
 */
class EmailProcessIntegrationTest extends TestCase
{
    /**
     * @var string Путь к тестовому файлу
     */
    private $testFile;

    /**
     * Создает тестовый файл перед каждым тестом
     */
    protected function setUp(): void
    {
        $this->testFile = __DIR__ . '/test_emails.txt';
    }

    /**
     * Удаляет тестовый файл после каждого теста
     */
    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    /**
     * Тестирует обработку реального файла с email адресами
     * 
     * Сценарий:
     * 1. Создаем реальный файл на диске
     * 2. Записываем в него тестовые email
     * 3. Запускаем обработку
     * 4. Проверяем результат
     */
    public function testProcessEmailsWithRealFile()
    {
        $emails = [
            'test@gmail.com',
            'invalid-email',
            'test@localhost.local',
            'info@yandex.ru'
        ];
        
        file_put_contents($this->testFile, implode("\n", $emails));
        
        $result = EmailProcess::processEmails($this->testFile);
        $data = $result->toArray();
        
        $this->assertIsArray($data);
        $this->assertCount(4, $data);
    }

    /**
     * Тестирует производительность с большим файлом
     * 
     * Проверяет, что метод может обработать 1000 email адресов
     * без проблем с памятью или временем выполнения
     */
    public function testProcessEmailsWithLargeFile()
    {
        $emails = [];
        for ($i = 0; $i < 1000; $i++) {
            $emails[] = "test{$i}@gmail.com";
        }
        
        file_put_contents($this->testFile, implode("\n", $emails));
        
        $result = EmailProcess::processEmails($this->testFile);
        $this->assertCount(1000, $result->toArray());
    }
}