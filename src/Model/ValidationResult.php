<?php

declare(strict_types=1);

namespace Aovchinnikova\Hw15\Model;

class ValidationResult
{
    // Нарушение: Публичные свойства (нарушение инкапсуляции).
    // Решение: Сделать private + геттеры.
    public $email;
    public $isValidFormat;
    public $hasValidDNS;

    public function __construct($email, $isValidFormat, $hasValidDNS)
    {
        $this->email = $email;
        $this->isValidFormat = $isValidFormat;
        $this->hasValidDNS = $hasValidDNS;
    }

    // Отсутствует метод для удобного преобразования в массив.
    // Правильный код:
    // public function toArray(): array {
    //     return [
    //         'email' => $this->email,
    //         'isValid' => $this->isValid(),
    //         'details' => [
    //             'format' => $this->isValidFormat,
    //             'dns' => $this->hasValidDNS
    //         ]
    //     ];
    // }
}
