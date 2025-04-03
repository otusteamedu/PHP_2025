<?php

namespace App\Commands;

use App\Indexes\OtusShopIndex;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class OtusShopSearchCommand extends Command
{
    protected static string $name = "shop:search";
    protected static string $description = "Поиск по данным индекса";

    /**
     * @return void
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function handle() {
        $otusShop = new OtusShopIndex();

        $query = [
            'bool' => [
                'must' => []
            ]
        ];

        if ($this->inputService->exist('category')) {
            $value = $this->inputService->get('category');

            if (empty($value) === false) {
                $query['bool']['must'][] = [
                    'match' => [
                        'category' => $value,
                    ]
                ];
            }
        }

        if ($this->inputService->exist('title')) {
            $value = $this->inputService->get('title');

            if (empty($value) === false) {
                $query['bool']['must'][] = [
                    'match' => [
                        'title' => $value,
                    ]
                ];
            }
        }

        if ($this->inputService->exist('max-price')) {
            $value = $this->inputService->get('max-price');

            if (empty($value) === false) {
                $query['bool']['filter'][] = [
                    'range' => [
                        'price' => [
                            'lt' => $value,
                        ]
                    ]
                ];
            }
        }

        if ($this->inputService->exist('min-price')) {
            $value = $this->inputService->get('min-price');

            if (empty($value) === false) {
                $query['bool']['filter'][] = [
                    'range' => [
                        'price' => [
                            'gt' => $value,
                        ]
                    ]
                ];
            }
        }

        $otusShop = $otusShop->search($query);

        $indexData = array_map(function ($item) {
            return [
                'title' => $item['_source']['title'],
                'sku' => $item['_source']['sku'],
                'category' => $item['_source']['category'],
                'price' => $item['_source']['price'],
            ];
        }, $otusShop['hits']['hits']);

        $mask = "|    %-60.100s    |    %s    |    %-20.50s    |    %s    |\n";

        printf($mask, "Наименование", "sku", "Категория", "Цена");
        foreach ($indexData as $array) {
            printf($mask, $array['title'], $array['sku'], $array['category'], $array['price']);
        }
    }
}