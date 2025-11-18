<?php

declare(strict_types=1);

namespace MaksimSkoropispcev\Math\Application;

class Math
{
    public function exponentiation(mixed $num, mixed $exponent): int|float|object
    {
        return pow($num, $exponent);
    }
}