<?php

declare(strict_types=1);

namespace App\Service;

use JsonException;

class TableService
{
    /**
     * @throws JsonException
     */
    public function showBooksAsTable(array $dataList): void
    {
        $output = fopen('php://output', 'w');

        foreach ($dataList as $data) {
            $preparedData = [
                'title' => $data['title'] ?? '',
                'sku' => $data['sku'] ?? '',
                'category' => $data['category'] ?? '',
                'price' => $data['price'] ?? '',
                'stocks' => isset($data['stock']) ? json_encode($data['stock'], JSON_THROW_ON_ERROR) : '',
            ];

            fputcsv($output, $preparedData, escape: $escape = "\\");
        }

        fclose($output);
    }
}
