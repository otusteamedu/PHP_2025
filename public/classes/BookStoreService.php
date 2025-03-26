<?php declare(strict_types=1);

namespace classes;

use Elastic\Elasticsearch;

class BookStoreService
{
    private const CATEGORIES = [
        'fantasy',
        'historical',
        'science',
        'education'
    ];

    private const YEARS = [
        '2020', '2021', '2022', '2023', '2024', '2025'
    ];

    private const BOOK_TITLES = [
        'Рыцари республики издание',
        'Рыцарство в наши века версия',
        'Рыбак или рыцарь издание',
        'Железный равин издание',
        'Роксана версия',
    ];

    private const MIN_PRICE = 1000;
    private const MAX_PRICE = 3000;
    private const DEMO_BOOKS_AMOUNT = 1000;

    //TODO в .env переносим
    private const ELASTICSEARCH_DOCKER_HOST = 'es01:9200';

    private const BASE_INDEX = 'book_store';


    protected $client;


    function __construct()
    {
        $this->client = Elasticsearch\ClientBuilder::create()
            ->setHosts([self::ELASTICSEARCH_DOCKER_HOST])
            ->build();
    }

    public function fillTheStore():void
    {
        $params = ['body' => []];

        for ($i = 1; $i <= self::DEMO_BOOKS_AMOUNT; $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => self::BASE_INDEX,
                    '_id'    => $i
                ]
            ];


            $randCategory = self::CATEGORIES[rand(0, count(self::CATEGORIES) - 1)];

            $randTitle = self::BOOK_TITLES[rand(0, count(self::BOOK_TITLES) - 1)].
                ' '.self::YEARS[rand(0, count(self::YEARS) - 1)].' '.$i;

            $params['body'][] = [
                'title' => $randTitle,
                'price' => rand(self::MIN_PRICE, self::MAX_PRICE),
                'category' => $randCategory,
                'stocks' => rand(10, 30)
            ];
        }

        if (!empty($params['body'])) {
            $this->client->bulk($params);
        }

    }

    public function search(array $searchQuery):array
    {
        $params = [
            'index' => self::BASE_INDEX,
            'body'  => [
                'query' => $searchQuery
            ]
        ];
        return $this->client->search($params)->asArray();
    }

}