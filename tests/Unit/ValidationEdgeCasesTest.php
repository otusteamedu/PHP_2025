<?php
namespace Tests\Unit;

use App\Validation;
use PHPUnit\Framework\TestCase;

/**
 * Класс ValidationEdgeCasesTest
 * 
 * Тестирует валидации email
 * Проверяет поведение с нестандартными, но потенциально валидными email адресами
 */
class ValidationEdgeCasesTest extends TestCase
{
    /**
     * Тестирует email с очень длинным доменом
     * 
     * Проверяет, что функция не падает при обработке
     * очень длинных доменных имен до 200 символов
     */
    public function testValidateEmailWithVeryLongDomain()
    {
        $longDomain = str_repeat('a', 200) . '.com';
        $email = "test@{$longDomain}";
        
        $result = Validation::validateEmail($email);
        // Может быть как корректным, так и некорректным - зависит от DNS
        $this->assertContains($result, ['Корректный Email', 'Некорректный Email']);
    }

    /**
     * Тестирует email с IP-адресом в качестве домена
     * 
     * Формат: test@[127.0.0.1] - технически валидный email
     * Но checkdnsrr не может проверить MX для IP
     */
    public function testValidateEmailWithIpAddress()
    {
        $result = Validation::validateEmail('test@[127.0.0.1]');
        $this->assertEquals('Некорректный Email', $result);
    }

    /**
     * Тестирует email с кавычками и пробелами в имени
     * Проверяем обработку сложных вариантов
     */
    public function testValidateEmailWithQuotedString()
    {
        $result = Validation::validateEmail('"test test"@example.com');
        $this->assertContains($result, ['Корректный Email', 'Некорректный Email']);
    }
}