<?php

class ElasticsearchLib
{
    private $obClient;
    private $sIndexName;
    private $fBooks;


    public function __construct(){
        $this->loadEnv();
        require_once 'vendor/autoload.php';
        
        $this->obClient = Elasticsearch\ClientBuilder::create()
            ->setHosts([$_ENV['ELASTICSEARCH_HOST']])
            ->build();

        $this->sIndexName = $_ENV['ELASTICSEARCH_INDEX'];
        $this->fBooks = $_ENV['BOOKS_JSON_FILE'];
    }

    private function loadEnv(){
        if (!file_exists('.env')) {
            die("Файл конфигурации .env не найден");
        }
        $sLines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($sLines as $sLine) {
            if (strpos(trim($sLine), '#') === 0) {
                continue;
            }
            
            if (strpos($sLine, '=') !== false) {
                list($key, $value) = explode('=', $sLine, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }

    public function getBooksFile(){
        return $this->fBooks;
    }

    
    public function checkIndex(){
        if(!($this->obClient->indices()->exists(['index' => $this->sIndexName]))){$this->createIndex();};
    }

    public function createIndex(){
        $fBooks = $this->getBooksFile();

        if (!file_exists($fBooks)) {
            die("Файл с книгами '{$fBooks}' не найден");
        }

        $arParams = [
            'index' => $this->sIndexName,
            'body' => [
                'settings' => [
                    'analysis' => [
                        'filter' => [
                            'russian_stop' => [
                                'type' => 'stop',
                                'stopwords' => '_russian_'
                            ],
                            'russian_stemmer' => [
                                'type' => 'stemmer',
                                'language' => 'russian'
                            ]
                        ],
                        'analyzer' => [
                            'russian' => [
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'russian_stop',
                                    'russian_stemmer'
                                ]
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'russian'
                        ],
                        'category' => [
                            'type' => 'keyword'
                        ],
                        'price' => [
                            'type' => 'float'
                        ],
                        'stock' => [
                            'type' => 'integer'
                        ]
                    ]
                ]
            ]
        ];

        if ($this->obClient->indices()->exists(['index' => $this->sIndexName])) {
            $this->obClient->indices()->delete(['index' => $this->sIndexName]);
        }

        $this->obClient->indices()->create($arParams);
        $fBooks = $this->getBooksFile();
        if (!file_exists($fBooks)) {
            throw new Exception("Файл с книгами '{$fBooks}' не найден");
        }
        
        $sBooks = json_decode(file_get_contents($fBooks), true);
        $this->indexBooks($sBooks);
        sleep(2);
        return count($sBooks);
    }

    public function indexBooks($arBooks)
    {
        $arParams = ['body' => []];

        foreach ($arBooks as $arBook) {
            $arParams['body'][] = [
                'index' => [
                    '_index' => $this->sIndexName,
                    '_id' => $arBook['id']
                ]
            ];
            $arParams['body'][] = $arBook;
        }

        $this->obClient->bulk($arParams);
    }

    public function searchBooks($query, $sCtegory = null, $iMaxPrice = null, $bHideUnavailable = true)
    {
        $arSearchParams = [
            'index' => $this->sIndexName,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => []
                    ]
                ],
                'sort' => [
                    '_score' => ['order' => 'desc']
                ],
                'size' => 50
            ]
        ];

        if (!empty($query)) {
            $arSearchParams['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^2', 'category'],
                    'fuzziness' => 'AUTO',
                    'analyzer' => 'russian'
                ]
            ];
        }

        if ($sCtegory) {
            $arSearchParams['body']['query']['bool']['filter'][] = [
                'term' => ['category' => $sCtegory]
            ];
        }

        if ($iMaxPrice !== null) {
            $arSearchParams['body']['query']['bool']['filter'][] = [
                'range' => ['price' => ['lte' => $iMaxPrice]]
            ];
        }

        if ($bHideUnavailable ) {
            $arSearchParams['body']['query']['bool']['filter'][] = [
                'range' => ['stock' => ['gt' => 0]]
            ];
        }

        if (empty($arSearchParams['body']['query']['bool']['must']) && 
            empty($arSearchParams['body']['query']['bool']['filter'])) {
            $arSearchParams['body']['query'] = ['match_all' => new stdClass()];
        }

        $arResponse = $this->obClient->search($arSearchParams);

        $arResults = [];
        foreach ($arResponse['hits']['hits'] as $hit) {
            $arResults[] = [
                'title' => $hit['_source']['title'],
                'category' => $hit['_source']['category'],
                'price' => $hit['_source']['price'],
                'stock' => $hit['_source']['stock'],
                'score' => $hit['_score']
            ];
        }

        return $arResults;
    }
}
