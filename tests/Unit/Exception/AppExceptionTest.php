<?php
namespace Tests\Unit\Exception;

use App\Exception\AppException;
use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;

/**
 * Класс AppExceptionTest
 * Тестирует пользовательское исключение приложения
 * Проверяет создание исключения и преобразование в HTTP ответ
 */
class AppExceptionTest extends TestCase
{
    /**
     * Тестирует конструктор с параметрами по умолчанию
     * Проверяет, что исключение создается даже без параметров
     */
    public function testConstructorWithDefaultValues()
    {
        $exception = new AppException();
        $this->assertEquals('', $exception->getMessage());
    }

    /**
     * Тестирует конструктор с пользовательским сообщением
     */
    public function testConstructorWithCustomMessage()
    {
        $message = 'Test error message';
        $exception = new AppException($message);
        $this->assertEquals($message, $exception->getMessage());
    }

    /**
     * Тестирует метод toResponse() с кодом статуса по умолчанию (500)
     * 
     * Проверяет:
     * 1. Возвращается объект JsonResponse
     * 2. Статус код = 500
     * 3. Тело ответа содержит переданное сообщение
     */
    public function testToResponseWithDefaultStatusCode()
    {
        $message = 'Error occurred';
        $exception = new AppException($message);
        
        $response = $exception->toResponse();
        
        // Проверяем тип ответа
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        
        // Проверяем содержимое
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(['error' => $message], $content);
    }

    /**
     * Тестируем метод toResponse() с пользовательским кодом статуса
     * Проверяет возможность задать свой HTTP статус код
     */
    public function testToResponseWithCustomStatusCode()
    {
        $message = 'Not found';
        $statusCode = 404;
        $exception = new AppException($message, $statusCode);
        
        $response = $exception->toResponse();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(['error' => $message], $content);
    }

    /**
     * Тестирует наследование от базового Exception 
     * Проверяет, что AppException является частью стандартной частью исключений
     */
    public function testExceptionExtendsBaseException()
    {
        $exception = new AppException('Test');
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}