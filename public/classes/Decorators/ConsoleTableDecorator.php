<?php declare(strict_types=1);

namespace classes\Decorators;

class ConsoleTableDecorator implements SearchResultsDecoratorInterface
{
    public function decorate(array $arSearchResults):void
    {
        if ($arSearchResults['hits']['total']['value'] <= 0) {
            throw new \RuntimeException('Error: hits not found'.PHP_EOL);
        }

        $table = new \LucidFrame\Console\ConsoleTable();

        $table = $table->setHeaders(['id', 'score', 'title', 'price', 'category', 'stocks']);

        foreach ($arSearchResults['hits']['hits'] as $product) {
            $table = $table->addRow([
                $product['_id'],
                $product['_score'],
                $product['_source']['title'],
                $product['_source']['price'],
                $product['_source']['category'],
                $product['_source']['stocks']
            ]);
        }

        $table->setPadding(2)->display();
    }

}