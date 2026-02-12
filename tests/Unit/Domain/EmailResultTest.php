<?php
namespace Tests\Unit\Domain;

use App\Domain\EmailResult;
use PHPUnit\Framework\TestCase;

/**
 * Класс EmailResultTest
 * Тестирует DTO EmailResult
 * Отвечает за хранение и предоставление результатов валидации
 */
class EmailResultTest extends TestCase
{
    /**
     * Тестирует конструктор и метод toArray
     * Проверяет:
     * 1. Что объект создается корректно
     * 2. Что метод toArray возвращает исходный массив
     * 3. Сохранение всех переданных данных
     */
    public function testConstructorAndToArray()
    {
        // Подготавливаем тестовые данные
        $results = [
            'test@example.com' => 'Корректный Email',
            'invalid@email' => 'Некорректный Email'
        ];
        
        // Создаем объект EmailResult
        $emailResult = new EmailResult($results);
        
        // Проверяем, что toArray возвращает переданный массив
        $this->assertEquals($results, $emailResult->toArray());
    }

    /**
     * Тестирует работу с пустым массивом
     * Проверяет корректность обработки
     */
    public function testEmptyResults()
    {
        $emailResult = new EmailResult([]);
        $this->assertEmpty($emailResult->toArray());
        $this->assertIsArray($emailResult->toArray());
    }

    /**
     * Тестирует сохранение ключей и значений массива
     */
    public function testPreservesKeysAndValues()
    {
        $results = [
            'key1' => 'value1',
            'key2' => 'value2'
        ];
        
        $emailResult = new EmailResult($results);
        $resultArray = $emailResult->toArray();
        
        // Проверяем наличие всех ключей
        $this->assertArrayHasKey('key1', $resultArray);
        $this->assertArrayHasKey('key2', $resultArray);
        
        // Проверяем значения
        $this->assertEquals('value1', $resultArray['key1']);
        $this->assertEquals('value2', $resultArray['key2']);
    }
}