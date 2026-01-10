<?php

declare(strict_types=1);

namespace Dinargab\Homework5\Service;


class EmailResultFormatter implements FormatterInterface
{

    /**
     * Форматирует массив объектов ValidationResult в строку HTML.
     *
     * @param ValidationResult[] $inputArray Массив объектов ValidationResult для форматирования.
     * @return string Отформатированная строка с результатами, разделенными <br>.
     */
    public function format(array $inputArray): string
    {
        $output = "";
        foreach ($inputArray as $element) {
            $output .= $element->getInputValue() . " is " . ($element->isValid() ? "valid" : "invalid");
            if (!$element->isValid()) {
                $output .= ", Error message:" . $element->getError();
            }
            $output .= "<br>";
        }
        return $output;
    }
}
