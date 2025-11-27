<?php

class ParenthesisValidator
{
    const ACCEPTABLE_CHARACTERS = ['(', ')'];
    private array $characters;

    public function __construct(string $str)
    {
        $this->characters = str_split($str);
    }

    /**
     * @throws Exception
     */
    public function validate(): void
    {
        $this->checkAcceptableCharacters();
    }

    /**
     * @throws Exception
     */
    private function checkAcceptableCharacters():void
    {
        if(!$this->characters)
            throw new Exception('An empty string was passed');
        if(!array_diff($this->characters, self::ACCEPTABLE_CHARACTERS) == [])
            throw new Exception('String must not contain characters other than "(" and ")"');
    }
}