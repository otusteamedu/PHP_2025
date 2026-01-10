<?php
declare(strict_types=1);


namespace App\Domain\Mapper;


use App\Domain\Book\Book;
use App\Domain\Book\BookStock;
use App\Domain\Repository\BookFilter;
use App\Domain\Repository\Pager;

class BookMapper
{
    public function buildFilter(array $payload): BookFilter
    {
        $filter = new BookFilter(Pager::fromPage(
            (int)$payload['page'] ?? null,
            (int)$payload['limit'] ?? null));

        if (isset($payload['category'])) {
            $filter->setCategory($payload['category']);
        }
        if (isset($payload['title'])) {
            $filter->setTitle($payload['title']);
        }
        if (isset($payload['minPrice'])) {
            $filter->setMinPrice((int)$payload['minPrice']);
        }
        if (isset($payload['maxPrice'])) {
            $filter->setMaxPrice((int)$payload['maxPrice']);
        }
        if (isset($payload['inStock'])) {
            $filter->setIsInStock($payload['inStock'] === 'true');
        }

        return $filter;
    }

    public function mapEntity(array $data): Book
    {
        $item = new Book(
            $data['_id'],
            $data['_source']['title'],
            $data['_source']['sku'],
            $data['_source']['category'],
            $data['_source']['price'],
        );
        foreach ($data['_source']['stock'] as $stock) {
            $item->adStock(new BookStock(
                $stock['shop'],
                $stock['stock'],
            ));
        }
        return $item;
    }

}