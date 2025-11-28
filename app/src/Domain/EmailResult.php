<?php
namespace App\Domain;

// Сбор данных
class EmailResult
{
    private array $results;

    // Получаю массив результатов
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    // Возвращаю данные массива для сериализации в JSON
    public function toArray(): array
    {
        return $this->results;
    }
}
?>