<?php
namespace Tests\Functional;

use PHPUnit\Framework\TestCase;

/**
 * Класс IndexTest
 * Функциональный тест: проверяет работу всего приложения целиком
 * Тест публичного эндпоинта (index.php)
 * Эмуляция HTTP запросов и проверка JSON ответа
 */
class IndexTest extends TestCase
{
    /**
     * @var string Путь к файлу с email адресами
     */
    private $testEmailsFile;

    /**
     * Определяем путь к файлу emails.txt перед каждым тестом
     */
    protected function setUp(): void
    {
        $this->testEmailsFile = __DIR__ . '/../../emails.txt';
    }

    /**
     * Удаляем тестовый файл после каждого теста
     */
    protected function tearDown(): void
    {
        if (file_exists($this->testEmailsFile)) {
            unlink($this->testEmailsFile);
        }
    }

    /**
     * Тестирует корректную работу index.php с валидными данными
     * 
     * Сценарий:
     * 1. Создаем файл emails.txt с набором тестовых email
     * 2. Запускаем index.php
     * 3. Перехватываем вывод
     * 4. Проверяем JSON структуру ответа
     * 
     */
    public function testIndexPageWithValidEmails()
    {
        // Создаем тестовый файл
        file_put_contents($this->testEmailsFile, 
            "test@gmail.com\ninvalid-email\ninfo@yandex.ru"
        );
        
        // Захватываем вывод index.php
        ob_start();
        require __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();
        
        // Проверяем, что вывод - валидный JSON
        $this->assertJson($output);
        
        // Проверяем структуру данных
        $data = json_decode($output, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('test@gmail.com', $data);
        $this->assertArrayHasKey('invalid-email', $data);
    }

    /**
     * Тестирует работу index.php с пустым файлом
     * Нет email для обработки
     * Ожидаем: пустой JSON объект {}
     * 
     */
    public function testIndexPageWithEmptyFile()
    {
        file_put_contents($this->testEmailsFile, '');
        
        ob_start();
        require __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();
        
        $this->assertJson($output);
        $data = json_decode($output, true);
        $this->assertEmpty($data);
    }

    /**
     * Тестирует обработку ошибок при отсутствии файла
     * 
     * Сценарий:
     * 1. Не создаем emails.txt
     * 2. Запускаем index.php
     * 3. Проверяем, что получаем корректный JSON с ошибкой
     */
    public function testIndexPageWithNoFile()
    {
        ob_start();
        require __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();
        
        $this->assertJson($output);
        $data = json_decode($output, true);
        
        // Проверяем наличие сообщения об ошибке
        $this->assertArrayHasKey('error', $data);
    }
}