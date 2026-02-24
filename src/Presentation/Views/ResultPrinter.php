<?php

namespace EmailsVerifier\Presentation\Views;

use EmailsVerifier\Presentation\Services\VerificationResultProcessor;

readonly class ResultPrinter
{
    public static function printResults(array $results): void
    {
        $processedResults = VerificationResultProcessor::processResults($results);

        echo "Проверка Email [Всего: {$processedResults['total_count']} | Валидны: {$processedResults['valid_count']} | Невалидны: {$processedResults['invalid_count']}]:\n";
        echo "==========================\n";

        foreach ($results as $item) {
            $email = $item->email;
            if ($item->isValid) {
                echo "$email : ok.\n";
            } else {
                $strErrors = implode(' | ', array_map(function ($error) {
                    return $error->getMessage();
                }, $item->errors));
                echo "$email : $strErrors\n";
            }
        }
    }
}
