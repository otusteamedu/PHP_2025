<?php

namespace Blarkinov\ElasticApp\CLI;

class Response
{
    public function send(int $code, string $message, array $viewData = [])
    {
        echo $message;

        if ($viewData)
            $this->displayResult($viewData);

        exit($code);
    }

    private function displayResult(array $data): void
    {
        $headers = ['title', 'sku', 'category', 'price', 'stock'];

        $rows = [];

        // foreach ($data as $hit) {

        //     $source = $hit['_source'];

        //     $stockStr = '';
        //     if (isset($source['stock']) && is_array($source['stock'])) {
        //         $stockParts = [];
        //         foreach ($source['stock'] as $s) {
        //             $shop = isset($s['shop']) ? $s['shop'] : '';
        //             $quantity = isset($s['stock']) ? $s['stock'] : '';
        //             $stockParts[] = "$shop ($quantity)";
        //         }
        //         $stockStr = implode(', ', $stockParts);
        //     }

        //     $rows[] = [
        //         'title' => isset($source['title']) ? $source['title'] : '',
        //         'sku' => isset($source['sku']) ? $source['sku'] : '',
        //         'category' => isset($source['category']) ? $source['category'] : '',
        //         'price' => isset($source['price']) ? $source['price'] : '',
        //         'stock' => $stockStr,
        //     ];
        // }

        // $colWidths = [];
        // foreach ($headers as $i => $header) {
        //     $maxWidth = mb_strlen($header);
        //     foreach ($rows as $row) {
        //         $maxWidth = max($maxWidth, mb_strlen((string)$row[$header]));
        //     }
        //     $colWidths[$i] = $maxWidth;
        // }

        // $format = '';
        // foreach ($colWidths as $width) {
        //     $format .= " | %-" . $width . "s";
        // }
        // $format .= " |\n";

        // printf($format, ...$headers);

        // $separator = '';
        // foreach ($colWidths as $width) {
        //     $separator .= '+-' . str_repeat('-', $width) . '-';
        // }

        // $separator .= '+';
        // echo $separator . "\n";

        // foreach ($rows as $row) {
        //     printf($format, $row['title'], $row['sku'], $row['category'], $row['price'], $row['stock']);
        // }

        // echo $separator . "\n";

        echo "\n".count($data)."\n";
    }
}
