<?php
namespace Tests\Unit;

use App\Validation;
use PHPUnit\Framework\TestCase;

/**
 * Класс ValidationTest
 * 
 * Тестирует статический метод validateEmail() класса Validation
 * Проверяет различные сценарии валидации email адресов:
 * - корректные email с существующими MX записями
 * - корректные email без MX записей
 * - некорректные форматы email
 * - граничные случаи
 */
class ValidationTest extends TestCase
{
    /**
     * Тестирует валидацию email с различными входными данными
     * 
     * @dataProvider emailProvider - использует провайдер данных для множественных сценариев
     * @param string $email - проверяемый email адрес
     * @param string $expected - ожидаемый результат валидации
     */
    public function testValidateEmail(string $email, string $expected)
    {
        $result = Validation::validateEmail($email);
        $this->assertEquals($expected, $result);
    }

    /**
     * Провайдер данных для testValidateEmail
     * 
     * Возвращает набор тестовых случаев:
     * 1. Валидный email с существующим MX (обычно работает)
     * 2. Валидный email без MX записи
     * 3. Невалидный формат (отсутствует @)
     * 4. Невалидный формат (несколько @)
     * 5. Невалидный формат (спецсимволы)
     * 6. Валидный email с заведомо существующим MX
     * 
     * @return array
     */
    public function emailProvider(): array
    {
        return [
            'valid email with mx record' => ['test@gmail.com', 'Корректный Email'],
            'valid email without mx record' => ['test@localhost.local', 'Некорректный Email'],
            'invalid format - no @' => ['test.com', 'Некорректный Email'],
            'invalid format - multiple @' => ['test@test@test.com', 'Некорректный Email'],
            'invalid format - special chars' => ['test@te$st.com', 'Некорректный Email'],
            'domain with mx record' => ['info@yandex.ru', 'Корректный Email'],
        ];
    }

    /**
     * Тестирует случай с заведомо несуществующим доменом
     * 
     * @runInSeparateProcess - запускает тест в отдельном процессе
     * Важно: результат может зависеть от наличия интернета и DNS
     */
    public function testValidateEmailWithNoMxRecord()
    {
        $result = Validation::validateEmail('test@nonexistentdomain12345.com');
        $this->assertEquals('Некорректный Email', $result);
    }
}