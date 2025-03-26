<?php

namespace classes\Commands;

use classes\CommandInterface;
use classes\BookStoreService;
use classes\Decorators\ConsoleTableDecorator;

class Search implements CommandInterface
{
    protected BookStoreService $bookStoreService;
    protected ConsoleTableDecorator $consoleTableDecorator;

    function __construct(){
        $this->bookStoreService = new BookStoreService();
        $this->consoleTableDecorator = new ConsoleTableDecorator();
    }

    private static string $name = 'es:search';

    public function execute(array $argv = [])
    {
        list($category, $title, $priceGraterThan) = $argv;

        $searchQuery = [
            "bool" => [
                "must" => [
                    [
                        "match" => [
                            "category" => $category
                        ]
                    ],
                    [
                        "match" => [
                            "title" => [
                                "query" => $title,
                                "fuzziness" => "AUTO"
                            ]
                        ]
                    ],
                    [
                        "range" => [
                            "price" => [
                                "gte" => $priceGraterThan
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $arSearchResults = $this->bookStoreService->search($searchQuery);
        $this->consoleTableDecorator->decorate($arSearchResults);
    }

    public static function getName():string
    {
        return static::$name;
    }

}