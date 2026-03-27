<?php

declare(strict_types=1);

namespace App\Service;

use JsonException;
use RuntimeException;

class FileLoaderService
{
    /**
     * @throws JsonException
     */
    public function loadJsonLineListFromFile(string $file): array
    {
        $fileLineList = file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
        $resultList = [];

        foreach ($fileLineList as $fileLine) {
            $decodedLine = json_decode($fileLine, true, 512, JSON_THROW_ON_ERROR);

            if ($decodedLine === null) {
                throw new RuntimeException("Error occurred with line $fileLine");
            }

            $resultList[] = $decodedLine;
        }

        return $resultList;
    }
}
