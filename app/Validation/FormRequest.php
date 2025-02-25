<?php

namespace App\Validation;

use App\Exceptions\EmptyPostException;
use App\Exceptions\UnbalancedStringException;

class FormRequest
{
    protected const string EMPTY_STRING_MESSAGE = 'Строка для проверки отсутствует в запросе.';
    protected const string UNBALANCED_STRING_MESSAGE =
        "Строка ':string' не сбалансирована по открывающим/закрывающим скобкам.";

    /**
     * @param array $post
     * @param string $key
     * @return string
     * @throws EmptyPostException
     * @throws UnbalancedStringException
     */
    public function validate(array $post, string $key): string
    {
        if (empty($post[$key])) {
            throw new EmptyPostException(self::EMPTY_STRING_MESSAGE);
        }

        if (new ParenthesesBalance()->validate($post[$key]) === false) {
            $message = str_replace(':string', $post[$key], self::UNBALANCED_STRING_MESSAGE);
            throw new UnbalancedStringException($message);
        }

        return $post[$key];
    }
}