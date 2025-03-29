<?php declare(strict_types=1);

namespace App;

use App\Service\BookSearchService;
use App\Service\ElasticsearchService;
use LucidFrame\Console\ConsoleTable;

class App
{
    private array $args;
    private ElasticsearchService $elasticsearchService;
    private BookSearchService $bookSearchService;

    public function __construct(array $args)
    {
        $this->args = $this->parseArgs($args);
        $this->elasticsearchService = new ElasticsearchService(['http://elasticsearch:9200']);
        $this->bookSearchService = new BookSearchService($this->elasticsearchService);
    }

    public function run(): void
    {
        if (isset($this->args['fill'])) {
            $this->fillBooks();
            return;
        }

        if (isset($this->args['delete'])) {
            $this->deleteBooks();
            return;
        }

        if (isset($this->args['search'])) {
            $this->searchBooks();
            return;
        }

        echo "Usage: php index.php --fill | --delete | --search --query=текст [--category='исторический роман'] [--pricefrom=1000] [--priceto=3000]\n";
    }

    private function fillBooks(): void
    {
        if ($this->bookSearchService->checkIfIndexExists()) {
            echo "⚠️ Индекс books уже добавлен в Elasticsearch\n";
            return;
        }

        $this->bookSearchService->fill();
        echo "✅ Книги загружены в Elasticsearch\n";
    }

    private function deleteBooks(): void
    {
        if (!$this->bookSearchService->checkIfIndexExists()) {
            echo "⚠️ Индекс books не создан в Elasticsearch\n";
            return;
        }

        $this->bookSearchService->delete();
        echo "❌ Индекс удален\n";
    }

    private function searchBooks(): void
    {
        if (!$this->bookSearchService->checkIfIndexExists()) {
            echo "⚠️ Индекс books не создан в Elasticsearch\n";
            return;
        }

        $query = $this->args['query'] ?? '';
        $category = $this->args['category'] ?? null;
        $priceFrom = isset($this->args['pricefrom']) ? (int)$this->args['pricefrom'] : null;
        $priceTo = isset($this->args['priceto']) ? (int)$this->args['priceto'] : null;

        if (empty($query)) {
            echo "⚠️ Добавьте запрос --query\n";
            return;
        }

        try {
            $data = $this->bookSearchService->search($query, $category, $priceFrom, $priceTo);

            if (empty($data['hits']['hits'])) {
                echo "⚠️ Результатов не найдено.\n";
                return;
            }

            $table = new ConsoleTable();
            $table
                ->addHeader('Название')
                ->addHeader('Категория')
                ->addHeader('Цена');

            foreach ($data['hits']['hits'] as $book) {
                $title = $book['_source']['title'];
                $category = str_pad($book['_source']['category'], 30);
                $price = (string)$book['_source']['price'];

                $table->addRow()
                    ->addColumn($title)
                    ->addColumn($category)
                    ->addColumn($price);
            }

            $table->display();
        } catch (\Exception $e) {
            echo "❌ Ошибка при выполнении поиска: " . $e->getMessage() . "\n";
        }
    }

    private function parseArgs(array $argv): array
    {
        $args = [];
        foreach ($argv as $arg) {
            if (preg_match('/^--([^=]+)(?:=(.+))?$/', $arg, $matches)) {
                $args[$matches[1]] = $matches[2] ?? true;
            }
        }
        return $args;
    }
}
