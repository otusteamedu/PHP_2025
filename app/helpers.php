<?php

if (!function_exists('getStoragePath')) {
    /**
     * @param string $path
     * @return string
     */
    function getStoragePath(string $path): string {
        return __DIR__ . "/../storage/" . $path;
    }
}

if (!function_exists('getFileContents')) {
    /**
     * @param string $path
     * @return string|null
     */
    function getFileContents(string $path): ?string {
        $storagePath = getStoragePath($path);

        if (file_exists($storagePath)) {
            $file = file_get_contents($storagePath);
        } else {
            $file = null;
        }

        return $file;
    }
}

if (!function_exists('dd')) {
    /**
     * @param $values
     * @return string|null
     */
    function dd(...$values): ?string {
        $output = '';
        foreach ($values as $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } else if (empty($value)) {
                $value = '(EMPTY)';
            } else {
                $value = (string)$value;
            }

            $output .= PHP_EOL . $value . PHP_EOL;
        }

        echo $output;
        exit;
    }
}

