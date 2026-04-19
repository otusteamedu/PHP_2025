<?php

declare(strict_types=1);

namespace App\Storage;

use App\Model\Book;
use RuntimeException;

/**
 * Слой хранилища для работы с Elasticsearch.
 * Реализует интерфейс BookStorageInterface.
 */
final class ElasticsearchStorage implements BookStorageInterface
{
    /** Имя индекса в Elasticsearch */
    private const INDEX = 'otus-shop';

    private string $host;

    /**
     * Конструктор.
     *
     * @param string $host URL Elasticsearch (по умолчанию http://localhost:9200)
     */
    public function __construct(string $host = 'http://localhost:9200')
    {
        $this->host = $host;
    }

    /**
     * Выполнить HTTP-запрос к Elasticsearch.
     *
     * @param string $method HTTP-метод (GET, POST, PUT, DELETE)
     * @param string $path Путь запроса
     * @param array<string, mixed>|null $body Тело запроса
     * @return array|bool Ответ или false при ошибке
     */
    private function request(string $method, string $path, ?array $body = null): array|bool
    {
        $url = $this->host . $path;
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error !== '') {
            throw new RuntimeException('Ошибка CURL: ' . $error);
        }

        if ($httpCode >= 400) {
            return false;
        }

        return $response ? json_decode($response, true) : true;
    }

    /**
     * Пересоздать индекс с русским анализатором.
     * Удаляет старый индекс и создаёт новый с настройками для русского языка.
     */
    public function recreateIndex(): void
    {
        // Удаляем старый индекс, если есть
        $this->request('DELETE', '/' . self::INDEX);
        
        // Создаём новый индекс с анализатором для русского языка
        $this->request('PUT', '/' . self::INDEX, [
            'settings' => [
                'analysis' => [
                    'filter' => [
                        // Стeммер для русского языка (стемминг = выделение корня слова)
                        'russian_stemmer' => [
                            'type' => 'stemmer',
                            'language' => 'russian',
                        ],
                    ],
                    'analyzer' => [
                        'ru_text' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'russian_stemmer',
                            ],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'sku' => [
                        'type' => 'keyword', // Точное значение для поиска по артикулу
                    ],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'ru_text', // Используем русский анализатор
                    ],
                    'category' => [
                        'type' => 'keyword',
                    ],
                    'price' => [
                        'type' => 'integer',
                    ],
                    'stock' => [
                        'type' => 'integer',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Массовая индексация книг.
     *
     * @param Book[] $books Массив книг для индексации
     * @return int Количество успешно проиндексированных книг
     */
    public function bulkIndex(array $books): int
    {
        if ($books === []) {
            return 0;
        }

        // Формируем тело запроса в формате NDJSON (newline delimited JSON)
        $body = '';
        foreach ($books as $book) {
            if (!$book instanceof Book) {
                continue;
            }

            $header = ['index' => ['_index' => self::INDEX, '_id' => $book->sku]];
            $body .= json_encode($header) . "\n";
            $body .= json_encode($book->toDocument()) . "\n";
        }

        if ($body === '') {
            return 0;
        }

        // Выполняем bulk-запрос
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->host . '/_bulk',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        
        if (($result['errors'] ?? false) === true) {
            throw new RuntimeException('Ошибка массовой индексации.');
        }

        return (int) ($result['items'] ?? []) ? count($books) : 0;
    }

    /**
     * Поиск книг с фильтрами.
     *
     * @param string|null $query Поисковый запрос
     * @param string|null $category Фильтр по категории
     * @param int|null $maxPrice Фильтр по максимальной цене
     * @param bool $inStockOnly Фильтр "только в наличии"
     * @return Book[] Найденные книги
     */
    public function search(
        ?string $query = null,
        ?string $category = null,
        ?int $maxPrice = null,
        bool $inStockOnly = false,
    ): array {
        $filter = [];
        $must = [];

        // Фильтр по категории
        if ($category !== null && $category !== '') {
            $filter[] = [
                'term' => [
                    'category' => $category,
                ],
            ];
        }

        // Фильтр по цене
        if ($maxPrice !== null) {
            $filter[] = [
                'range' => [
                    'price' => [
                        'lte' => $maxPrice, // Меньше или равно
                    ],
                ],
            ];
        }

        // Фильтр по наличию
        if ($inStockOnly) {
            $filter[] = [
                'range' => [
                    'stock' => [
                        'gt' => 0, // Больше нуля
                    ],
                ],
            ];
        }

        $query = trim((string) $query);

        // П��исковый запрос с нечётким matching (fuzziness)
        // Позволяет находить слова с опечатками
        if ($query !== '') {
            $must[] = [
                'match' => [
                    'title' => [
                        'query' => $query,
                        'fuzziness' => 'AUTO', // Автоматический уровень нечёткости
                        'prefix_length' => 1, // Первая буква должна совпадать
                    ],
                ],
            ];
        }

        // Формируем тело запроса
        $body = [
            'query' => [
                'bool' => array_filter([
                    'must' => $must,
                    'filter' => $filter,
                ]),
            ],
            // Сортировка: сначала по релевантности (_score), затем по цене
            'sort' => [
                ['_score' => ['order' => 'desc']],
                ['price' => ['order' => 'asc']],
            ],
            'size' => 20,
        ];

        // Если нет поискового запроса, сортируем только по цене
        if ($must === []) {
            $body['query'] = [
                'bool' => array_filter([
                    'filter' => $filter,
                ]),
            ];
            $body['sort'] = [
                ['price' => ['order' => 'asc']],
            ];
        }

        $response = $this->request('POST', '/' . self::INDEX . '/_search', $body);

        if (!is_array($response) || !isset($response['hits']['hits'])) {
            return [];
        }

        $hits = $response['hits']['hits'];
        $result = [];

        foreach ($hits as $hit) {
            $source = $hit['_source'] ?? [];

            $result[] = new Book(
                sku: (string) ($source['sku'] ?? $hit['_id'] ?? ''),
                title: (string) ($source['title'] ?? ''),
                category: (string) ($source['category'] ?? ''),
                price: (int) ($source['price'] ?? 0),
                stock: (int) ($source['stock'] ?? 0),
            );
        }

        return $result;
    }
}