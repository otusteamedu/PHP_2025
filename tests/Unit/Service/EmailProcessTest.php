<?php
namespace Tests\Unit\Service;

use App\Service\EmailProcess;
use App\Domain\EmailResult;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

/**
 * Класс EmailProcessTest
 * 
 * Тестирует сервис обработки email из файла
 * Использует виртуальную файловую систему vfsStream для тестирования
 */
class EmailProcessTest extends TestCase
{
    /**
     * @var vfsStreamDirectory Корневая директория виртуальной ФС
     */
    private $root;

    /**
     * Создает виртуальную файловую систему перед каждым тестом
     */
    protected function setUp(): void
    {
        $this->root = vfsStream::setup('emails');
    }

    /**
     * Тестирует обработку файла с корректными данными
     * 
     * Сценарий:
     * 1. Создаем виртуальный файл с набором email
     * 2. Запускаем обработку
     * 3. Проверяем результаты
     * 
     * Проверяет:
     * - Возвращается объект EmailResult
     * - Количество результатов соответствует количеству email
     * - Сохраняются все ключи
     */
    public function testProcessEmailsWithValidFile()
    {
        // Подготавливаем тестовые email адреса
        $emails = [
            'test@gmail.com',
            'invalid-email',
            'test@localhost.local',
            'info@yandex.ru'
        ];
        
        // Создаем виртуальный файл с содержимым
        $file = vfsStream::newFile('emails.txt')
            ->withContent(implode("\n", $emails))
            ->at($this->root);
        
        // Запускаем обработку
        $result = EmailProcess::processEmails($file->url());
        
        // Проверяем тип результата
        $this->assertInstanceOf(EmailResult::class, $result);
        
        // Получаем данные
        $data = $result->toArray();
        
        // Проверяем количество и наличие ключей
        $this->assertCount(4, $data);
        $this->assertArrayHasKey('test@gmail.com', $data);
        $this->assertArrayHasKey('invalid-email', $data);
    }

    /**
     * Тестирует обработку пустого файла
     * 
     * Тут делаем, что файл существует, но не содержит данных
     * Ожидаем: пустой массив результатов
     */
    public function testProcessEmailsWithEmptyFile()
    {
        $file = vfsStream::newFile('emails.txt')
            ->withContent('')
            ->at($this->root);
        
        $result = EmailProcess::processEmails($file->url());
        
        $this->assertInstanceOf(EmailResult::class, $result);
        $this->assertEmpty($result->toArray());
    }

    /**
     * Тестирует обработку файла с пустыми строками
     * 
     * Проверяет, что FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
     * корректно обрабатывает пустые строки в файле
     */
    public function testProcessEmailsWithFileContainingEmptyLines()
    {
        $content = "test@gmail.com\n\n\ninvalid-email\n\n";
        $file = vfsStream::newFile('emails.txt')
            ->withContent($content)
            ->at($this->root);
        
        $result = EmailProcess::processEmails($file->url());
        
        // Должны получить только 2 email, пустые строки пропущены
        $this->assertCount(2, $result->toArray());
    }

    /**
     * Тестирует обработку несуществующего файла
     * 
     * Проверяет, что метод выбрасывает исключение при попытке
     * прочитать несуществующий файл
     */
    public function testProcessEmailsThrowsExceptionForNonExistentFile()
    {
        $this->expectException(\Exception::class);
        EmailProcess::processEmails(vfsStream::url('emails/nonexistent.txt'));
    }

    /**
     * Тестирует реальную валидацию email
     * Проверяет, что процесс валидации возвращает корректные статусы
     */
    public function testProcessEmailsWithRealValidation()
    {
        $file = vfsStream::newFile('emails.txt')
            ->withContent("test@gmail.com\ninvalid-email")
            ->at($this->root);
        
        $result = EmailProcess::processEmails($file->url());
        $data = $result->toArray();
        
        // Проверяем результаты валидации
        $this->assertEquals('Корректный Email', $data['test@gmail.com']);
        $this->assertEquals('Некорректный Email', $data['invalid-email']);
    }
}