<?php

declare(strict_types=1);

use App\Application\SearchBooksService;
use App\Infrastructure\ElasticsearchBookRepository;
use App\UserInterface\SearchBooksCommand;

return [
    'elasticsearchBookRepository' => function() {
        return new ElasticsearchBookRepository(
        );
    },

    'searchBooksService' => function($container) {
        return new SearchBooksService(
            $container->get('elasticsearchBookRepository')
        );
    },

    'searchBooksCommand' => function($container) {
        return new SearchBooksCommand(
            $container->get('searchBooksService'),
        );
    }
];
