<?php

interface MagicInterface
{
    /**
     * Устанавливает хранимое значение внутри объекта класса.
     * @param string $value
     * @return \MagicInterface
     */
    public function setValue(string $value): MagicInterface;

    /**
     * Возвращает текущее хранимое значение из объекта класса.
     * @return string
     */
    public function getValue(): string;

    /**
     * Разбивает хранимое значение в массив с использованием разделителя.
     * @param string $delimiter
     * @return \MagicInterface
     */
    public function split(string $delimiter): MagicInterface;

    /**
     * Склеивает массив в хранимое значение с использованием "клея".
     * @param string $glue
     * @return \MagicInterface
     */
    public function glue(string $glue): MagicInterface;

    /**
     * Приводит хранимое значение к верхнему регистру.
     * @return \MagicInterface
     */
    public function toUpperCase(): MagicInterface;

    /**
     * Переворачивает строку задом наперёд.
     * @return \MagicInterface
     */
    public function reverse(): MagicInterface;
}

class Magic implements MagicInterface
{
    private string|array $value = '';

    public function __construct()
    {
        $code = @file_get_contents(__FILE__);

        // Убираем комменты, чтобы закомментированные вызовы не выполнялись
        $codeNoComments = preg_replace('!/\*.*?\*/!s', '', $code);
        $codeNoComments = preg_replace('!//.*$!m', '', $codeNoComments);
        $codeNoComments = preg_replace('!#.*$!m', '', $codeNoComments);

        $methods = get_class_methods(self::class);

        // Поиск вызовов в синтаксисе JS через точку и вызовов в синтаксисе PHP через ->
        // и их последовательное выполнение
        if (preg_match_all('/(\.|->)\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\((.*?)\)/s', $codeNoComments, $calls, PREG_SET_ORDER)) {
            $obj = $this;

            foreach ($calls as $call) {
                $name = $call[2];

                if (!in_array($name, $methods)) {
                    continue;
                }

                $arg = trim($call[3] ?? '');

                $obj = $obj->$name($arg);
            }

            if (is_string($obj)) {
                echo $obj;
            }

            exit;
        }
    }

    public function setValue(string $value): MagicInterface
    {
        $this->value = $value;
        return $this;
    }

    public function getValue(): string
    {
        return is_array($this->value) ? implode(',', $this->value) : $this->value;
    }

    public function split(string $delimiter): MagicInterface
    {
        if (is_string($this->value)) {
            $this->value = explode($delimiter, $this->value);
        }
        return $this;
    }

    public function glue(string $glue): MagicInterface
    {
        if (is_array($this->value)) {
            $this->value = implode($glue, $this->value);
        }
        return $this;
    }

    public function toUpperCase(): MagicInterface
    {
        if (is_string($this->value)) {
            $this->value = strtoupper($this->value);
        } elseif (is_array($this->value)) {
            $this->value = array_map('strtoupper', $this->value);
        }
        return $this;
    }

    public function reverse(): MagicInterface
    {
        if (is_string($this->value)) {
            $this->value = strrev($this->value);
        } elseif (is_array($this->value)) {
            $this->value = array_map('strrev', $this->value);
        }
        return $this;
    }
}

$m = new Magic();
$result = $m
    . setValue('2022-12-29-otus')
    . split('-')
    . toUpperCase()
    . glue('_')
    . reverse()
    . getValue();
echo $result;  // SUTO_92_21_2202
