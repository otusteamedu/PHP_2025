<?php

class ParenthesisValidator
{
    const ACCEPTABLE_CHARACTERS = ['(', ')'];
    const PAIRED_PARENTHESIS = '()';
    private string $parenthesis;
    private array $characters;

    public function __construct(string $str)
    {
        $this->parenthesis = $str;
        $this->characters = str_split($str);
    }

    /**
     * @throws Exception
     */
    public function validate(): void
    {
        $this->checkAcceptableCharacters();
        $copyParenthesis = $this->parenthesis;
        while ($this->isStringContainsPairedParenthesis($copyParenthesis)) {
            $this->removePairedParenthesis($copyParenthesis);
        }

        if($copyParenthesis != '') {
            throw new Exception('Parenthesis are placed incorrectly');
        }
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

    private function removePairedParenthesis(string &$str): void
    {
        $str = str_replace(self::PAIRED_PARENTHESIS, '', $str);
    }

    private function isStringContainsPairedParenthesis(string $str): bool
    {
        return str_contains($str, self::PAIRED_PARENTHESIS);
    }
}