<?php

use JetBrains\PhpStorm\NoReturn;

if (!function_exists('dd')) {
    /**
     * @param $values
     * @return string|null
     */
    #[NoReturn] function dd(...$values): ?string {
        $output = '';
        foreach ($values as $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } else if (empty($value)) {
                $value = '(EMPTY)';
            } else if (is_object($value)) {
                $value = json_encode((array)$value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                $value = (string)$value;
            }

            $output .= PHP_EOL . $value . PHP_EOL;
        }

        echo $output;
        exit;
    }
}