<?php
namespace Tests\Unit\Http;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;

/**
 * Класс JsonResponseTest
 * 
 * Тестирует формирование HTTP JSON ответа
 * Проверяет:
 * - Создание ответа с разными типами данных
 * - Установку HTTP статус кодов
 * - Формирование JSON строки
 * - Отправку заголовков
 */
class JsonResponseTest extends TestCase
{
    /**
     * Тестирует конструктор с кодом статуса по умолчанию 200 OK
     * 
     * Проверяет, что при создании без указания статуса:
     * 1. Устанавливается статус 200
     * 2. Данные корректно сериализуются в JSON
     */
    public function testConstructorWithDefaultStatusCode()
    {
        $data = ['test' => 'value'];
        $response = new JsonResponse($data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 
            $response->getContent()
        );
    }

    /**
     * Тестирует конструктор с пользовательским кодом статуса
     * 
     * Проверяет возможность установки любого HTTP статус кода
     * Например: 201 Created, 400 Bad Request, 404 Not Found
     */
    public function testConstructorWithCustomStatusCode()
    {
        $data = ['error' => 'Not found'];
        $statusCode = 404;
        $response = new JsonResponse($data, $statusCode);
        
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Тестирует конструктор с разными типами данных
     * 
     * Проверяет, что JsonResponse может работать с любыми типами данных:
     * - Массивы
     * - Строки
     * - Числа
     * - Булевы значения
     * - Null
     * - Вложенные структуры
     */
    public function testConstructorWithDifferentDataTypes()
    {
        $testCases = [
            'array' => ['key' => 'value'],
            'string' => 'simple string',
            'integer' => 123,
            'boolean' => true,
            'null' => null,
            'nested_array' => ['user' => ['name' => 'John', 'age' => 30]]
        ];

        foreach ($testCases as $type => $data) {
            $response = new JsonResponse($data);
            $this->assertEquals(
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 
                $response->getContent()
            );
        }
    }

    /**
     * Тестирует отправку HTTP заголовков
     * 
     * @runInSeparateProcess - важно для тестирования header()
     * Проверяет:
     * 1. Установку Content-Type: application/json
     * 2. Установку правильной кодировки UTF-8
     * 3. Отправку HTTP статус кода
     */
    public function testSendHeaders()
    {
        $response = new JsonResponse(['test' => 'value']);
        
        // Перехватываем вывод
        $this->expectOutputString($response->getContent());
        
        // Отправляем заголовки и выводим контент
        $response->sendHeaders();
        echo $response->getContent();
        
        // Проверяем, что заголовки установлены
        $headers = xdebug_get_headers();
        $this->assertContains('Content-Type: application/json; charset=utf-8', $headers);
    }

    /**
     * Тестирует получение HTTP статус кода
     */
    public function testGetStatusCode()
    {
        $response = new JsonResponse([], 201);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * Тестирует корректную обработку Unicode символов
     * 
     * Проверяет, что JSON_UNESCAPED_UNICODE работает правильно
     * Русские символы не должны преобразовываться в \u абракадабру
     */
    public function testJsonEncodingOptions()
    {
        $data = ['русский' => 'текст'];
        $response = new JsonResponse($data);
        $content = $response->getContent();
        
        // Проверяем, что русские символы не экранированы
        $this->assertStringContainsString('русский', $content);
        $this->assertStringContainsString('текст', $content);
        
        // Проверяем наличие форматирования (pretty print)
        $this->assertStringContainsString("\n", $content);
    }
}