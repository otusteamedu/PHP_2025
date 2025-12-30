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
    private static Magic $current;

    public function __construct()
    {
        self::$current = $this;
    }

    public function __toString(): string
    {
        return '';
    }

    public static function getCurrent(): ?Magic
    {
        return self::$current;
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

function setValue(string $value): void
{
    Magic::getCurrent()->setValue($value);
}

function split(string $delimiter): void
{
    Magic::getCurrent()->split($delimiter);
}

function toUpperCase(): void
{
    Magic::getCurrent()->toUpperCase();
}

function glue(string $glue): void
{
    Magic::getCurrent()->glue($glue);
}

function reverse(): void
{
    Magic::getCurrent()->reverse();
}

function getValue(): string
{
    return Magic::getCurrent()->getValue();
}

$magic = new Magic();
$result = $magic
    . setValue('2022-12-29-otus')
    . split('-')
    . toUpperCase()
    . glue('_')
    . reverse()
    . getValue();
echo $result;  // SUTO_92_21_2202
