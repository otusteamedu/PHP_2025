<?php

namespace Blarkinov\ElasticApp\Service;

use Blarkinov\ElasticApp\Service\ElasticSearch\ElasticSearch;
use Exception;

class Validator
{

    private const MESSAGE_BAD_ARGUMENT = "bad argument or tags.\nArgument - search string, tags:\n--category=CATEGORY - search category\n--price=PRICE - product price\n--quantity=QUANTITY - product quantity\n--enabled - is there any in stock";

    public function __construct(private array $args) {}

    public function validate()
    {
        $this->mainValidate();
        $this->paramValidate();
    }


    private function mainValidate(): bool
    {
        if (count($this->args) === 1)
            throw new Exception(self::MESSAGE_BAD_ARGUMENT);

        $this->args = array_slice($this->args, 2);

        if (empty($this->args))
            return true;

        $pattern = "/[^a-zA-Z0-9\-\s\=]/u";

        foreach ($this->args as $key => $value) {
            if ($key === 0)
                continue;

            $matches = preg_match($pattern, $value, $matches);

            if (!empty($matches))
                throw new Exception(self::MESSAGE_BAD_ARGUMENT);

            if ($value[0] !== '-' || $value[1] !== '-')
                throw new Exception(self::MESSAGE_BAD_ARGUMENT);
        }

        return true;
    }

    private function paramValidate(): bool
    {
        foreach ($this->args as $value) {
            $value = explode("=", $value);

            if (!in_array(
                $value[0],
                [
                    '--' . ElasticSearch::PARAM_FILTER_CATEGORY,
                    '--' . ElasticSearch::PARAM_FILTER_ENABLED,
                    '--' . ElasticSearch::PARAM_FILTER_MIN_PRICE,
                    '--' . ElasticSearch::PARAM_FILTER_MAX_PRICE,
                    '--' . ElasticSearch::PARAM_FILTER_QUANTITY
                ]
            ))
                throw new Exception('unknown tag');

            if (
                (
                    $value[0] === '--' . ElasticSearch::PARAM_FILTER_CATEGORY ||
                    $value[0] === '--' . ElasticSearch::PARAM_FILTER_MIN_PRICE ||
                    $value[0] === '--' . ElasticSearch::PARAM_FILTER_MAX_PRICE ||
                    $value[0] === '--' . ElasticSearch::PARAM_FILTER_QUANTITY
                ) && count($value) === 1
            )
                throw new Exception('empty value in tag');

            if (
                $value[0] === '--' . ElasticSearch::PARAM_FILTER_MIN_PRICE ||
                $value[0] === '--' . ElasticSearch::PARAM_FILTER_MAX_PRICE ||
                $value[0] === '--' . ElasticSearch::PARAM_FILTER_QUANTITY
            ) {
                $matches = preg_match("/[^0-9]/", $value[1], $matches);
                if (!empty($matches))
                    throw new Exception('price or quantity must be a number');
            }
        }

        return true;
    }
}
