<?php 

function checkBrackets(string $string): bool {

    if (empty($string)) {
        return false;
    }

    $count = 0;
    for ($i = 0; $i < strlen($string); $i++) {
        $char = $string[$i];

        if ($char === '(') {
            $count++;
        } elseif ($char === ')') {
            $count--;
        } else {
            continue; 
        }

        if ($count < 0) {
            return false;
        }
    }

    return $count === 0;
}