<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Data\Mapper;

use Dinargab\Homework12\Data\Model\Book;

class BookMapper
{

    public static function dbToBook($array) :Book
    {
        return new Book(
            $array["title"],
            $array["sku"],
            $array["category"],
            (int) $array["price"],
            $array["stock"]
        );
    }
}
